/*
 * Copyright (c) Konrad Kollnig 2015.
 */

package com.ops.app.libs;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.net.Uri;
import android.util.Log;

import com.ops.app.R;
import com.ops.app.stores.News;
import com.ops.app.stores.Schedule;
import com.ops.app.stores.User;
import com.ops.app.views.LoginActivity;

import org.apache.http.HttpResponse;
import org.apache.http.HttpStatus;
import org.apache.http.StatusLine;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONObject;

import java.io.ByteArrayOutputStream;
import java.io.IOException;

public class Common {
    public static User user;
    public static Schedule schedule;
    public static News news;

    public static String buildUrl(String[][] params, String host, String jsonVersion) {
        Uri.Builder uri = Uri.parse(host)
                .buildUpon()
                .appendQueryParameter("jsonVersion", jsonVersion);

        for (String[] param : params) {
            if (param.length != 2) continue;
            uri.appendQueryParameter(param[0], param[1]);
        }

        return uri.build().toString();
    }

    public static Object fetchJson(String url) {
        return fetchJson(url, false);
    }

    public static Object fetchJson(String url, Boolean isArray) {
        String jsonString = downloadString(url);
        if (jsonString == null) return null;

        // Now parse the JSON into the json Object
        if (isArray) {
            jsonString = "{'data': " + jsonString + " }";
        }

        JSONObject jsonObject;
        try {
            jsonObject = new JSONObject(jsonString);

            if (!isArray) {
                return jsonObject;
            }

            return jsonObject.getJSONArray("data");
        } catch (Exception e) {
            return null;
        }
    }

    public static String downloadString(String url) {
        // Prepare request
        HttpClient httpclient = new DefaultHttpClient();
        HttpResponse response;
        String responseString;

        // Try to do the request
        try {
            response = httpclient.execute(new HttpGet(url));
            StatusLine statusLine = response.getStatusLine();
            if (statusLine.getStatusCode() == HttpStatus.SC_OK) {
                // Parse response into a string
                ByteArrayOutputStream out = new ByteArrayOutputStream();
                response.getEntity().writeTo(out);
                out.close();
                responseString = out.toString();

                return responseString;
            } else {
                //Closes the connection.
                response.getEntity().getContent().close();
                throw new IOException(statusLine.getReasonPhrase());
            }
        } catch (Exception e) {
            //String message = e.getMessage();
            //log(message);
            return null;
        }
    }

    /**
     * Helper function to log to the console
     *
     * @param message The message to be displayed
     */
    public static void log(String message) {
        Log.i(LoginActivity.class.getSimpleName(), message);
    }

    /**
     * Helper function to show MessageBoxes
     *
     * @param message The message to be displayed
     */
    public static void msgBox(final Activity destination, String message) {
        msgBox(destination, message, false);
    }

    /**
     * Helper function to show MessageBoxes with option to finish activity on okay click
     *
     * @param message The message to be displayed
     */
    public static void msgBox(final Activity destination, String message, final Boolean finishOnClick) {
        AlertDialog.Builder builder = new AlertDialog.Builder(destination);
        builder.setMessage(message)
                .setCancelable(false)
                .setPositiveButton(R.string.ok, new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int id) {
                        if (finishOnClick) {
                            destination.finish();
                        }
                    }
                });
        AlertDialog alert = builder.create();
        alert.show();
    }
}
