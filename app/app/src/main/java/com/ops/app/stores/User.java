/*
 * Copyright (c) Konrad Kollnig 2015.
 */

package com.ops.app.stores;

import android.content.Context;
import android.os.AsyncTask;

import com.ops.app.R;
import com.ops.app.libs.Common;
import com.ops.app.libs.Config;
import com.ops.app.libs.Model;
import com.ops.app.libs.Settings;
import com.ops.app.libs.Store;

import org.json.JSONException;
import org.json.JSONObject;

public class User extends Store {
    protected String[][] userModel = {
            {"token", "string"},
            {"username", "string"},
            {"firstname", "string"},
            {"lastname", "string"},
            {"klasse", "string"},
            {"info", "string"},
            {"isTeacher", "string"},
            {"last_update", "string"},
            {"success", "boolean"},
            {"error", "string"},
            {"gimmeInformation", "boolean"},
            {"password2change", "boolean"},
            {"noretry", "boolean"}
    };

    public String[][] getModel() {
        return userModel;
    }

    @Override
    public void load(String token) {
        // Build Url
        String[][] loginParams = {
                {"component", "auth"},
                {"view", "login"},
                {"os", "nativeAndroid"},
                {"token", token}
        };
        String loginUrl = Common.buildUrl(loginParams, Config.host, Config.jsonVersion);

        // Finally begin to load
        fire(loginUrl);
    }

    /**
     * Load User Object with combination of username and password
     *
     * @param username Entered username
     * @param password Entered password
     * @return
     */
    public void load(String username, String password) {
        // Build Url
        String[][] loginParams = {
                {"component", "auth"},
                {"view", "login"},
                {"os", "nativeAndroid"},
                {"username", username},
                {"password", password}
        };
        String loginUrl = Common.buildUrl(loginParams, Config.host, Config.jsonVersion);

        // Finally begin to load
        fire(loginUrl);
    }

    /**
     * Shortcut to this.getAt(0).get(key)
     *
     * @param key
     * @return
     */
    public Object get(String key) {
        return get(key, null);
    }


    public Object get(String key, Object _default) {
        Model record = this.getAt(0);

        if (record == null) return _default;
        return record.get(key);
    }

    public String getString(String key) {
        return (String) get(key);
    }

    public void logOut(Context context) {
        // Get token
        String token = Settings.get(context.getString(R.string.preference_token));

        // Logout from server
        logoutTask = new LogoutTask();
        logoutTask.execute(token);

        // Reset token
        Settings.set(context.getString(R.string.preference_token), null);
    }

    private LogoutTask logoutTask;

    public class LogoutTask extends AsyncTask<String, Void, Boolean> {
        @Override
        protected Boolean doInBackground(String... token) {
            if (token[0] == null) return false;

            // Build Url
            String[][] logoutParams = {
                    {"component", "auth"},
                    {"view", "update"},
                    {"token", token[0]}
            };
            String logoutUrl = Common.buildUrl(logoutParams, Config.host, Config.jsonVersion);


            // Download and parse json
            String jsonString = Common.downloadString(logoutUrl);
            if (jsonString == null) return false;

	        // Try to parse JSON
	        try {
                JSONObject json = new JSONObject(jsonString);

                // Success?
                return json.has("success") && json.getString("success").equals("true");
            } catch (JSONException jsonException) {
                return false;
            }
        }

        @Override
        protected void onPostExecute(Boolean success) {
            // Do nothing
        }
    }
}