<?php

/**
 * A facade class with wrapping functions to administer a dropbox account
 *
 * @copyright Copyright (C) 2011-2013 Michael De Wildt. All rights reserved.
 * @author Michael De Wildt (http://www.mikeyd.com.au/)
 * @license This program is free software; you can redistribute it and/or modify
 * 	  it under the terms of the GNU General Public License as published by
 * 	  the Free Software Foundation; either version 2 of the License, or
 * 	  (at your option) any later version.
 *
 * 	  This program is distributed in the hope that it will be useful,
 * 	  but WITHOUT ANY WARRANTY; without even the implied warranty of
 * 	  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * 	  GNU General Public License for more details.
 *
 * 	  You should have received a copy of the GNU General Public License
 * 	  along with this program; if not, write to the Free Software
 * 	  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA.
 */
class ESIGDS_DropboxFacade {

    const CONSUMER_KEY = 'zdn6lqvdny9nafx';
    const CONSUMER_SECRET = 'tqf7ipkb5hsies2';
    const RETRY_COUNT = 3;

    private static $instance = null;
    private
            $dropbox,
            $request_token,
            $access_token,
            $oauth_state,
            $oauth,
            $account_info_cache,
            $config,
            $directory_cache = array()

    ;

    public function __construct() {
        $this->init();
    }

    public function init() {
        
        $this->config = ESIGDS_Factory::get('config');

        if (!extension_loaded('curl')) {
            throw new Exception(sprintf(
                    __('The cURL extension is not loaded. %sPlease ensure its installed and activated.%s', eddds_get_textdomain()), '<a href="http://php.net/manual/en/curl.installation.php">', '</a>'
            ));
        }

        $this->oauth = new Dropbox_OAuth_Consumer_Curl(self::CONSUMER_KEY, self::CONSUMER_SECRET);
        $this->oauth_state = $this->config->get_option('oauth_state');
        $this->request_token = $this->get_token('request');
        $this->access_token = $this->get_token('access');

        // We requested a token. Get the access token
        if ($this->oauth_state == 'request') {

            // $this->config->drop_options();
            // $this->config->init_options();
            //If we have not got an access token then we need to grab one
            try {
                $this->oauth->setToken($this->request_token);
                $this->access_token = $this->oauth->getAccessToken();
                $this->oauth_state = 'access';
                $this->oauth->setToken($this->access_token);
                $this->save_tokens();

                //Supress the error because unlink, then init should be called
            } catch (Exception $e) {
                error_log(ESIG_DROPBOX_LABEL . ' init Caught exception: ' . $e->getCode() . ' : ' . $e->getMessage());

                // Bad token. Get another
                if ($e->getCode() == '401' && stripos($e->getMessage(), 'expired')) {
                    $this->oauth->resetToken();
                    $this->config->set_option('oauth_state', ''); //bad token
                    $this->request_access_token();
                }
            }

            // We have an access token
        } elseif ($this->oauth_state == 'access') {

            $this->oauth->setToken($this->access_token);

            //We don't have an access token. Request one.
        } else {


            $this->request_access_token();
        }

        $this->dropbox = new Dropbox_API($this->oauth);
        // $this->dropbox->setTracker(new EDDDS_UploadTracker());
    }

    /**
     * Acquire an unauthorised request token
     * @return void
     */
    public function request_access_token() {
        try {
            $this->request_token = $this->oauth->getRequestToken();
        } catch (Exception $e) {
            error_log(ESIG_DROPBOX_LABEL . " request_access_token Caught exception: " . $e->getCode() . " : " . $e->getMessage());
        }
        $this->oauth->setToken($this->request_token);
        $this->oauth_state = 'request';
        $this->save_tokens();
    }

    private function get_token($type) {
        $token = $this->config->get_option("{$type}_token");
        $token_secret = $this->config->get_option("{$type}_token_secret");

        $ret = new stdClass;
        $ret->oauth_token = null;
        $ret->oauth_token_secret = null;

        if ($token && $token_secret) {
            $ret = new stdClass;
            $ret->oauth_token = $token;
            $ret->oauth_token_secret = $token_secret;
        }

        return $ret;
    }

    public function is_authorized() {
        try {
            $this->get_account_info();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function auth_state() {
        return $this->config->get_option('oauth_state');
    }

    public function get_authorize_url() {
        return $this->oauth->getAuthoriseUrl(esigds_get_custom_menu_page());
    }

    public function get_account_info() {
        if (!isset($this->account_info_cache)) {
            $response = $this->dropbox->accountInfo();
            $this->account_info_cache = $response['body'];
        }

        return $this->account_info_cache;
    }

    private function save_tokens() {
        $this->config->set_option('oauth_state', $this->oauth_state);

        if ($this->request_token) {
            $this->config->set_option('request_token', $this->request_token->oauth_token);
            $this->config->set_option('request_token_secret', $this->request_token->oauth_token_secret);
        } else {
            $this->config->set_option('request_token', null);
            $this->config->set_option('request_token_secret', null);
        }

        if ($this->access_token) {
            $this->config->set_option('access_token', $this->access_token->oauth_token);
            $this->config->set_option('access_token_secret', $this->access_token->oauth_token_secret);
        } else {
            $this->config->set_option('access_token', null);
            $this->config->set_option('access_token_secret', null);
        }

        return $this;
    }

    public function upload_file($path, $file) {
        $i = 0;
        while ($i++ < self::RETRY_COUNT) {
            try {
                return $this->dropbox->putFile($file, $this->remove_secret($file), $path);
            } catch (Exception $e) {
                
            }
        }
        throw $e;
    }

    public function chunk_upload_file($path, $file, $processed_file) {
        $offest = $offest_id = null;
        if ($processed_file) {
            $offest = $processed_file->offset;
            $upload_id = $processed_file->uploadid;
        }

        return $this->dropbox->chunkedUpload($file, $this->remove_secret($file), $path, true, $offest, $upload_id);
    }

    public function delete_file($file) {
        return $this->dropbox->delete($file);
    }

    public function create_directory($path) {
        try {
            $this->dropbox->create($path);
        } catch (Exception $e) {
            
        }
    }

    public function get_directory_contents($path) {
        if (!isset($this->directory_cache[$path])) {
            try {
                $this->directory_cache[$path] = array();
                $response = $this->dropbox->metaData($path);

                foreach ($response['body']->contents as $val) {
                    if (!$val->is_dir) {
                        $this->directory_cache[$path][] = basename($val->path);
                    }
                }
            } catch (Exception $e) {
                $this->create_directory($path);
            }
        }

        return $this->directory_cache[$path];
    }

    public function unlink_account() {
        $this->oauth->resetToken();
        $this->request_token = null;
        $this->access_token = null;
        $this->oauth_state = null;

        return $this->save_tokens();
    }

    public static function remove_secret($file, $basename = true) {
        /* if (preg_match('/-eddds-secret$/', $file))
          $file = substr($file, 0, strrpos($file, '.'));

          if ($basename)
          return basename($file); */
        $file = preg_replace('/^.+[\\\\\\/]/', '', $file);

        return $file;
    }

}
