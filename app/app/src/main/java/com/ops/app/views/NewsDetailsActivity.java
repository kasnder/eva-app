/*
 * Copyright (c) Konrad Kollnig 2015.
 */

package com.ops.app.views;

import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.text.Html;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.TextView;

import com.ops.app.R;

public class NewsDetailsActivity extends ActionBarActivity {
	// Store the URL to the news' page
	String link;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_news_details);

        Intent intent = getIntent();

        // Go home if no extras
        if (!intent.hasExtra("content") || !intent.hasExtra("link") || !intent.hasExtra("title")) {
            finish();
        }

        // Set title
        String title = intent.getStringExtra("title");
        setTitle(title);

        // Display content
        String content = intent.getStringExtra("content");
        TextView contentView = (TextView) findViewById(R.id.news_details_content);
        contentView.setText(Html.fromHtml(content));
        //contentView.setMovementMethod(LinkMovementMethod.getInstance()); // Make links clickable

		// Fetch url
	    link = intent.getStringExtra("link");
    }

	@Override
	public boolean onCreateOptionsMenu (Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.activity_news_details, menu);
		return true;
	}

	@Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();
		switch (id) {
			case android.R.id.home:
				finish();
				return true;
			case R.id.menu_browser: // Open page in browser
				Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(link));
				startActivity(browserIntent);
				return true;
		}
        return super.onOptionsItemSelected(item);
    }
}
