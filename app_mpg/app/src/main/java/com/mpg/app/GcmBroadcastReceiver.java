/*
 * Copyright (c) Konrad Kollnig 2015.
 */

package com.mpg.app;

import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.media.RingtoneManager;
import android.net.Uri;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.v4.app.NotificationCompat;

import com.google.android.gms.gcm.GoogleCloudMessaging;
import com.mpg.app.libs.Settings;
import com.mpg.app.views.HomeActivity;

public class GcmBroadcastReceiver extends BroadcastReceiver {
    public static final int NOTIFICATION_ID = 1;

    @Override
    public void onReceive(Context context, Intent intent) {
        Bundle extras = intent.getExtras();
        GoogleCloudMessaging gcm = GoogleCloudMessaging.getInstance(context);
        // The getMessageType() intent parameter must be the intent you received
        // in your BroadcastReceiver.
        String messageType = gcm.getMessageType(intent);

        // Check if enabled..
        Boolean showNotification = PreferenceManager
                .getDefaultSharedPreferences(context)
                .getBoolean("checkbox_notifications", true);

	    // Check settings -- user logged in?
	    Settings.init(context);
	    String token = Settings.get(context.getString(R.string.preference_token));

		// Check received message
	    if (showNotification && !extras.isEmpty() &&
			    token != null &&
			    !token.equals("") &&
                GoogleCloudMessaging.
                        MESSAGE_TYPE_MESSAGE.equals(messageType)) {
            sendNotification(context, extras);
        }
    }

    // Put the message into a notification and post it.
    // This is just one simple example of what you might choose to do with
    // a GCM message.
    private void sendNotification(Context context, Bundle message) {
        // Show notification in notifications center
        NotificationManager mNotificationManager = (NotificationManager)
                context.getSystemService(Context.NOTIFICATION_SERVICE);

        if (message.containsKey("title") && message.containsKey("message")) {
            String title = message.getString("title");
            String text = message.getString("message");
            Uri notificationSound = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION);

	        // Show app and update on notification click
	        Intent intent = new Intent(context, HomeActivity.class);
	        intent.putExtra("force_update", true);
            PendingIntent contentIntent = PendingIntent.getActivity(context, 0,
		            intent, 0);

            NotificationCompat.Builder mBuilder =
                    new NotificationCompat.Builder(context)
                            .setSmallIcon(R.drawable.ic_stat_notification)
                            .setContentTitle(title)
                            .setStyle(new NotificationCompat.BigTextStyle()
                                    .bigText(text))
                            .setContentText(text)
                            .setAutoCancel(true)
                            .setContentIntent(contentIntent)
                                    //.setLights(Color.RED, 3000, 3000)
                            .setSound(notificationSound)
                            .setVibrate(new long[]{100, 100, 100, 100, 100});

            mNotificationManager.notify(NOTIFICATION_ID, mBuilder.build());
        }
    }
}
