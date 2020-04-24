/*
 * Copyright (c) Konrad Kollnig 2015.
 */

package com.ops.app.stores;

import com.ops.app.libs.Common;
import com.ops.app.libs.Config;
import com.ops.app.libs.Store;

public class News extends Store {
    private String _host;
    private String _jsonVersion;
    private Boolean _initialised;

    protected String[][] scheduleModel = {
            {"title", "string"},
            {"link", "string"},
            {"contentSnippet", "string"},
            {"content", "string"},
            {"publishedDate", "string"}
    };

    public String[][] getModel() {
        return scheduleModel;
    }

    @Override
    public void load(String url) {
        // Build Url
        String[][] newsParams = {
                {"component", "news"},
                {"view", "json"},
                {"contentOnly", "1"}
        };
        String newsUrl = Common.buildUrl(newsParams, Config.host, Config.jsonVersion);

        // Load Json
        fire(newsUrl);
    }

	// Abbreviation
	public void load () {
		this.load(null);
	}
}
