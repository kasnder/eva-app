/*
 * Copyright (c) Konrad Kollnig 2015.
 */

package com.mpg.app.stores;

import com.mpg.app.libs.Common;
import com.mpg.app.libs.Config;
import com.mpg.app.libs.Store;

public class Schedule extends Store {
    protected String[][] scheduleModel = {
            {"id", "string"},
            {"lehrerid", "string"},
            {"datum", "date"},
            {"klasse", "string"},
		    {"klassePatch", "string"},
            {"stunde", "string"},
		    {"stundePatch", "string"},
            {"bemerkung", "string"},
            {"fach", "string"},
            {"raum", "string"},
            {"vertretung", "string"},
            {"wichtig", "boolean"},
            {"aufsicht", "boolean"}
    };

    public String[][] getModel() {
        return scheduleModel;
    }

    @Override
    public void load(String token) {
        // Build Url
        String[][] scheduleParams = {
                {"component", "content"},
                {"view", "json"},
                {"token", token}
        };
        String scheduleUrl = Common.buildUrl(scheduleParams, Config.host, Config.jsonVersion);

        // Finally begin to load
        fire(scheduleUrl);
    }
}
