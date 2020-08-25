/*
 * Copyright (c) Konrad Kollnig 2015.
 */

package com.ops.app.views;


import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.annotation.TargetApi;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.text.Html;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;

import com.ops.app.R;
import com.ops.app.libs.Common;
import com.ops.app.libs.Model;
import com.ops.app.stores.News;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Locale;

/**
 * A simple {@link Fragment} subclass.
 * create an instance of this fragment.
 */
public class NewsFragment extends Fragment {

    /* UI Components */
    private View progressView;
    private ListView newsList;

    /**
     * Stores the screen orientation during loading
     */
    //protected int initialOrientation;

    /**
     * Array Adapter that will hold our ArrayList and display the items on the ListView
     */
    private NewsAdapter newsAdapter;

    /* Save restored ArrayList */
    ArrayList<Model> restoreNews;

    public NewsFragment() {
        // Required empty public constructor
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        Common.news = new News() {
            @Override
            public void onLoaded(Boolean success) {
                onNewsLoaded(success);
            }
        };
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        return inflater.inflate(R.layout.fragment_news, container, false);
    }

    @Override
    public void onActivityCreated(Bundle savedInstanceState) {
        super.onActivityCreated(savedInstanceState);

        // Try to restore a saved instance
        if (savedInstanceState != null) {
            try {
                ArrayList<Model> news = (ArrayList<Model>) savedInstanceState.getSerializable("news");

                // Check arrayLists
                if (news != null) {
                    restoreNews = news;
                }
            } catch (Exception e) {
                // Do nothing
            }
        }

        // Init UI
        newsList = (ListView) getActivity().findViewById(R.id.news_list);
        View emptyList = getActivity().getLayoutInflater().inflate(R.layout.list_news_empty, newsList, false);
        newsList.setEmptyView(emptyList);
        newsList.setOnItemClickListener(new ListView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view,
                                    int position, long id) {
                // Fetch content of entry
	            String link = Common.news.getAt(position).getString("link");

	            // TODO: Add proper parsing of "content" to re-enable NewsDetailsActivity
                //String title = Common.news.getAt(position).getString("title");
                //String content = Common.news.getAt(position).getString("content");

                // Build details intent
                /*Intent intent = new Intent(getActivity(), NewsDetailsActivity.class);

                intent.putExtra("title", title);
                intent.putExtra("content", content);
                intent.putExtra("link", link);

                startActivity(intent);*/

                Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(link));
                startActivity(browserIntent);
            }
        });

        progressView = getActivity().findViewById(R.id.news_progress);

        // Fill ListView
        newsAdapter = new NewsAdapter(getActivity(), R.layout.list_news_record, Common.news.arrayList);

        loadNews();
    }

    @Override
    public void onSaveInstanceState(Bundle savedInstanceState) {
        super.onSaveInstanceState(savedInstanceState);
        // Save UI state changes to the savedInstanceState.
        // This bundle will be passed to onCreate if the process is
        // killed and restarted.
        try {
            savedInstanceState.putSerializable("news", Common.news.arrayList);
        } catch (Exception e) {
            // Do nothing
        }
    }


    protected void loadNews() {
        // UI
        showProgress(true);

        // Stop everything
        stopNews();

        // Prevent screen rotation till loaded
        //initialOrientation = getActivity().getRequestedOrientation();
        //getActivity().setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_NOSENSOR);

        // Restore instance?
        if (restoreNews != null) {
            Common.news.restore(restoreNews);
            restoreNews = null;
            return;
        }

        // Use token or username + password to auth?
        Common.news.load();
    }

    protected void stopNews() {
        if (isLoading()) {
            Common.news.cancelLoading();
        }

        newsList.setAdapter(null);
        newsAdapter.notifyDataSetInvalidated();
    }

    public Boolean isLoading() {
        return Common.news.isLoading();
    }

    /**
     * Schedule loaded? Just let me finally display everything to the UI..
     *
     * @param success
     */
    public void onNewsLoaded(Boolean success) {
        if (!isAdded()) return;

        // Apply adapter to list
        newsList.setAdapter(newsAdapter);

        showProgress(false);

        // Restore screen orientation
        //getActivity().setRequestedOrientation(initialOrientation);

        // Anything went wrong?
        if (success) {
            // Update list
            newsAdapter.notifyDataSetChanged();
        }
    }

    /**
     * Shows the progress UI and hides it from the activity.
     */
    @TargetApi(Build.VERSION_CODES.HONEYCOMB_MR2)
    public void showProgress(final boolean show) {
        // On Honeycomb MR2 we have the ViewPropertyAnimator APIs, which allow
        // for very easy animations. If available, use these APIs to fade-in
        // the progress spinner.
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB_MR2) {
            int animTime = getResources().getInteger(android.R.integer.config_shortAnimTime);

            newsList.setVisibility(show ? View.GONE : View.VISIBLE);
            newsList.animate().setDuration(animTime).alpha(
                    show ? 0 : 1).setListener(new AnimatorListenerAdapter() {
                @Override
                public void onAnimationEnd(Animator animation) {
                    newsList.setVisibility(show ? View.GONE : View.VISIBLE);
                }
            });

            progressView.setVisibility(show ? View.VISIBLE : View.GONE);
            progressView.animate().setDuration(animTime).alpha(
                    show ? 1 : 0).setListener(new AnimatorListenerAdapter() {
                @Override
                public void onAnimationEnd(Animator animation) {
                    progressView.setVisibility(show ? View.VISIBLE : View.GONE);
                }
            });
        } else {
            // The ViewPropertyAnimator APIs are not available, so simply show
            // and hide the relevant UI components.
            progressView.setVisibility(show ? View.VISIBLE : View.GONE);
            newsList.setVisibility(show ? View.GONE : View.VISIBLE);
        }
    }

    /**
     * Custom ArrayAdapter to parse Schedule Store into the Schedule ListView
     */
    class NewsAdapter extends ArrayAdapter<Model> {
        final int resource;

        /**
         * Cache for all Ids
         */
        class ViewHolder {
            TextView recordTitle;
            TextView recordSnippet;
            TextView recordDate;
        }

        /**
         * Set up our ScheduleAdapter
         *
         * @param context  Activity - HomeTabbedActivity.this
         * @param resource Layout file - R.layout.list_schedule_record
         * @param items    ArrayList - Common.schedule.arrayList
         */
        NewsAdapter(Context context, int resource, List<Model> items) {
            super(context, resource, items);
            this.resource = resource;
        }

        @Override
        public View getView(int position, View convertView, ViewGroup parent) {
            //LinearLayout recordView;
            ViewHolder viewHolder;

            // Get the current record
            Model record = getItem(position);

            // Inflate the view
            if (convertView == null) {
                convertView = new LinearLayout(getContext());
                String inflater = Context.LAYOUT_INFLATER_SERVICE;
                LayoutInflater layoutInflater;
                layoutInflater = (LayoutInflater) getContext().getSystemService(inflater);
                layoutInflater.inflate(resource, (LinearLayout) convertView, true);

                // Fetch the text boxes from the record layout
                viewHolder = new ViewHolder();
                viewHolder.recordTitle = (TextView) convertView.findViewById(R.id.record_title);
                viewHolder.recordSnippet = (TextView) convertView.findViewById(R.id.record_snippet);
                viewHolder.recordDate = (TextView) convertView.findViewById(R.id.record_date);

                convertView.setTag(viewHolder);
            } else {
                // Restore Ids
                viewHolder = (ViewHolder) convertView.getTag();
            }

            // Show values
            viewHolder.recordTitle.setText(record.getString("title"));
            if (record.getString("contentSnippet") != null) {
                viewHolder.recordSnippet.setText(Html.fromHtml(record.getString("contentSnippet")));
            }
            String dateString = record.getString("publishedDate");
            try { // Show date
                Date date = new SimpleDateFormat("EEE, dd MMM yyyy HH:mm:ss Z", Locale.US).parse(dateString);
                SimpleDateFormat simpleDateFormat = new SimpleDateFormat("dd.MM.yyyy");
                viewHolder.recordDate.setText(simpleDateFormat.format(date));
            } catch (Exception e) {
                viewHolder.recordDate.setText(dateString);
            }

            return convertView;
        }
    }
}
