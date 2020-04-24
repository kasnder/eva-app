/*
 * Copyright (c) Konrad Kollnig 2015.
 */

package com.mpg.app.views;

import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.EditText;
import android.widget.TextView;

import com.mpg.app.R;
import com.mpg.app.libs.Common;
import com.mpg.app.libs.Config;

import org.json.JSONException;
import org.json.JSONObject;

public class SetupActivity extends ActionBarActivity {
	/* Fields */
	private EditText fieldForm;
	private EditText fieldTag;
	private EditText fieldOldPassword;
	private EditText fieldNewPassword;
	private EditText fieldNewPasswordRepetition;

	/* Intent values */
	private String token;
	private Integer isTeacher;
	private Boolean password2change;
	private Boolean gimmeInformation;

	/* Values */
	private String tag;
	private String form;
	private String passwordOld;
	private String passwordNew;
	private UpdateTask updateTask;

	@Override
	protected void onCreate (Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_setup);

		// Try to read out the extra of the intent
		Intent intent = getIntent();

		// Check existence
		if (!intent.hasExtra("token")
				|| !intent.hasExtra("firstname")
				|| !intent.hasExtra("lastname")
				|| !intent.hasExtra("isTeacher")
				|| !intent.hasExtra("token")
				|| (!intent.hasExtra("password2change")
				&& !intent.hasExtra("gimmeInformation"))) {
			goHome();
		}

		// Read
		token = intent.getStringExtra("token");
		String firstName = intent.getStringExtra("firstname");
		String lastName = intent.getStringExtra("lastname");
		isTeacher = intent.getIntExtra("isTeacher", -1);
		password2change = intent.getBooleanExtra("password2change", false);
		gimmeInformation = intent.getBooleanExtra("gimmeInformation", false);

		// Check values
		if (token.isEmpty()
				//|| firstName.isEmpty() // TODO WHY?!?
				//|| lastName.isEmpty()
				|| isTeacher == -1
				|| (!gimmeInformation && !password2change)) {
			goHome();
		}

		// Find views
		fieldForm = (EditText) findViewById(R.id.setup_form);
		fieldTag = (EditText) findViewById(R.id.setup_tag);
		fieldOldPassword = (EditText) findViewById(R.id.setup_password_old);
		fieldNewPassword = (EditText) findViewById(R.id.setup_password_new);
		fieldNewPasswordRepetition = (EditText) findViewById(R.id.setup_password_repetition);
		TextView introText = (TextView) findViewById(R.id.intro_text);

		// Check what to do..
		if (gimmeInformation) {
			fieldForm.setVisibility(View.VISIBLE);
			if (isTeacher > 0) {
				fieldTag.setVisibility(View.VISIBLE);
			}
		}

		if (password2change) {
			fieldOldPassword.setVisibility(View.VISIBLE);
			fieldNewPassword.setVisibility(View.VISIBLE);
			fieldNewPasswordRepetition.setVisibility(View.VISIBLE);
		}

		// Set intro text
		introText.setText(String.format(getText(R.string.setup_intro).toString(), firstName, lastName));
	}

	/**
	 * Setup complete button handler
	 *
	 * @param view Needed to handle button click..
	 */
	public void onClickComplete (View view) {
		if (updateTask != null) return;

		// Fetch values
		tag = fieldTag.getText().toString();
		form = fieldForm.getText().toString();
		passwordOld = fieldOldPassword.getText().toString();
		passwordNew = fieldNewPassword.getText().toString();
		String passwordRepetition = fieldNewPasswordRepetition.getText().toString();

		// Reset errors
		fieldOldPassword.setError(null);
		fieldNewPasswordRepetition.setError(null);

		// Password required + Not all password set
		if (password2change
				&& (passwordOld.isEmpty() || passwordNew.isEmpty() || passwordRepetition.isEmpty())) {
			Common.msgBox(this, getString(R.string.error_all_fields));
			return;
		}

		// Check passwords
		if (password2change && !passwordRepetition.equals(passwordNew)) {
			fieldNewPasswordRepetition.setError(getString(R.string.error_password_match));
			return;
		}

		// Request server
		updateTask = new UpdateTask();
		updateTask.execute();
	}

	@Override
	public boolean onCreateOptionsMenu (Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.activity_setup, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected (MenuItem item) {
		// Handle action bar item clicks here. The action bar will
		// automatically handle clicks on the Home/Up button, so long
		// as you specify a parent activity in AndroidManifest.xml.
		int id = item.getItemId();

		// Navigate back to Login screen
		if (id == R.id.menu_cancel) {
			Common.user.logOut(this);
			goHome();
			return true;
		}

		return super.onOptionsItemSelected(item);
	}

	private void goHome () {
		Intent intent = new Intent(this, HomeActivity.class);
		startActivity(intent);
		finish();
	}

	public class UpdateTask extends AsyncTask<Void, Void, String> {
		@Override
		protected String doInBackground (Void... params) {
			final String defaultError = getString(R.string.error_setup_failed);

			// Build Url
			String[][] setupParams = {
					{"component", "auth"},
					{"view", "update"},
					{"token", token},

					{"updateForm", Boolean.toString((gimmeInformation && isTeacher == 0) || (isTeacher != 0 && gimmeInformation && !form.isEmpty()))},
					{"form", form.toLowerCase()},

					{"updateTag", gimmeInformation.toString()},
					{"tag", tag},

					{"updatePass", password2change.toString()},
					{"oldPassword", passwordOld},
					{"password", passwordNew}
			};
			String setupUrl = Common.buildUrl(setupParams, Config.host, Config.jsonVersion);

			// Download JSON
			String jsonString = Common.downloadString(setupUrl);
			if (jsonString == null) return defaultError;

			// Try to parse JSON
			try {
				JSONObject json = new JSONObject(jsonString);

				// Error?
				if (json.has("error")) {
					return json.getString("error");
				}

				// Success?
				if (json.has("success") && json.getString("success").equals("true")) {
					return null;
				} else {
					return defaultError;
				}
			} catch (JSONException jsonException) {
				return defaultError;
			}
		}

		@Override
		protected void onPostExecute (String error) {
			updateTask = null;

			if (error == null || error.isEmpty()) {
				goHome();
			} else {
				Common.msgBox(SetupActivity.this, error);
			}
		}
	}
}
