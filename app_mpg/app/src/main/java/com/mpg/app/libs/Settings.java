/*
 * Copyright (c) Konrad Kollnig 2015.
 */

package com.mpg.app.libs;

import android.content.Context;
import android.content.SharedPreferences;
import android.preference.PreferenceManager;

public class Settings {
    private static SharedPreferences mSharedPreferences;

    public static void init(Context context) {
        // Only init once
        if (mSharedPreferences == null) {
            mSharedPreferences = PreferenceManager
                    .getDefaultSharedPreferences(context);
        }
    }

    /* String processing */
    public static String get(String key) {
        return get(key, null);
    }

    public static String get(String key, String defaultValue) {
        return mSharedPreferences.getString(key, defaultValue);
    }

    public static Boolean set(String key, String value) {
        SharedPreferences.Editor editor = mSharedPreferences.edit();
        editor.putString(key, value);
        return editor.commit();
    }

    /* Int processing */
    public static int getInt(String key) {
        return getInt(key, -1);
    }

    public static int getInt(String key, int defaultValue) {
        return mSharedPreferences.getInt(key, defaultValue);
    }

    public static Boolean setInt(String key, int value) {
        SharedPreferences.Editor editor = mSharedPreferences.edit();
        editor.putInt(key, value);
        return editor.commit();
    }
}
