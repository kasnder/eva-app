/*
 * Copyright (c) Konrad Kollnig 2015.
 */

package com.mpg.app.views;

import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.view.KeyEvent;
import android.view.View;
import android.view.inputmethod.EditorInfo;
import android.widget.EditText;
import android.widget.TextView;

import com.mpg.app.R;
import com.mpg.app.libs.Common;
import com.mpg.app.libs.Settings;


/**
 * A login screen that offers login via username/password.
 */
public class LoginActivity extends ActionBarActivity {
    // UI references.
    private EditText _usernameView;
    private EditText _passwordView;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

	    // Init settings
	    Settings.init(this);

        // Init layout
        _passwordView = (EditText) findViewById(R.id.password);
        _passwordView.setOnEditorActionListener(new TextView.OnEditorActionListener() {
            @Override
            public boolean onEditorAction(TextView textView, int id, KeyEvent keyEvent) {
                // actionDone IME_ACTION_DONE
                if (id == getResources().getInteger(R.integer.loginImeActionId)
                        || id == EditorInfo.IME_NULL
                        || id == EditorInfo.IME_ACTION_DONE
                        ) {
                    login();
                    return true;
                }
                return false;
            }
        });

        _usernameView = (EditText) findViewById(R.id.username);
        //_usernameView.setOnClickListener();
        String username = Settings.get(getString(R.string.preference_username));
        if (username != null) {
            _usernameView.setText(username);
            _passwordView.requestFocus();
        }

        // Username/Password to restore?
        Intent callIntent = getIntent();
        if (callIntent.hasExtra("username") && callIntent.hasExtra("password")) {
            username = callIntent.getStringExtra("username");
            String password = callIntent.getStringExtra("password");

            _usernameView.setText(username);
            _passwordView.setText(password);
            _passwordView.requestFocus();
        }

        // Message to display in LoginView?
        if (callIntent.hasExtra("message")) {
            String message = callIntent.getStringExtra("message");
            if (message == null) {
                return;
            }

            // Hide option to retry?
            Boolean noRetry = callIntent.getBooleanExtra("noRetry", true);

            if (noRetry) {
                Common.msgBox(this, message);
                return;
            }

            AlertDialog.Builder builder = new AlertDialog.Builder(this);
            builder.setMessage(message)
                    .setTitle(R.string.retry)
                    .setPositiveButton(android.R.string.yes, new DialogInterface.OnClickListener() {
                        @Override
                        public void onClick(DialogInterface dialog, int id) {
                            String token = Settings.get(getString(R.string.preference_token));
                            login(token);
                        }
                    })
                    .setNegativeButton(android.R.string.no, null);
            AlertDialog alert = builder.create();
            alert.show();
        }
    }

    /**
     * Login button handler
     *
     * @param view Needed to handle button click..
     */
    public void onClickSignIn(View view) {
        login(null);
    }

    /**
     * Attempts to sign in or register the account specified by the login form.
     */
    public void login() {
        login(null);
    }

    public void login(String token) {
        Intent intent = new Intent(this, HomeActivity.class);

        if (token == null) {
            String username = _usernameView.getText().toString();
            String password = _passwordView.getText().toString();

            intent.putExtra("username", username);
            intent.putExtra("password", password);
        } else {
            intent.putExtra("token", token);
        }

        startActivity(intent);
        finish();
    }
}