/*
 * Copyright (c) Konrad Kollnig 2015.
 */

package com.ops.app.stores;

import com.ops.app.libs.Common;
import com.ops.app.libs.Config;
import com.ops.app.libs.Store;

public class Schedule extends Store {
    protected String[][] scheduleModel = {
            {"id", "string"},
            {"lehrerid", "string"},
            {"datum", "date"},
            {"klasse", "string"},
            {"stunde", "string"},
            {"bemerkung", "string"},
            {"fach", "string"},
            {"raum", "string"},
            {"vertretung", "string"},
            {"klassePatch", "string"},
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
