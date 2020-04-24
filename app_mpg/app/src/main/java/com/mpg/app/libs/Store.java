/*
 * Copyright (c) Konrad Kollnig 2015.
 */

package com.mpg.app.libs;

import android.os.AsyncTask;

import org.json.JSONArray;
import org.json.JSONObject;
import org.json.JSONTokener;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.HashMap;

public abstract class Store implements Serializable {
	/**
	 * Make sure to assign a unique serialVersionUID
	 */
	private static final long serialVersionUID = 1L;

    /**
     * Store the data of the Store
     */
    public ArrayList<Model> arrayList = new ArrayList<>();

    /**
     * Store the currently running FetchTask
     */
    protected FetchTask _fetchTask;

    /**
     * Store errors while fetching
     */
    public String errorMessage = null;

    //protected String dateFormat;

    /**
     * Access to the ArrayList
     *
     * @return The ArrayList
     */
    public ArrayList<Model> getAll() {
        return arrayList;
    }

    /* ArrayList methods */
    public Model getAt(int index) {
        if (index < arrayList.size() && index >= 0) {
            return arrayList.get(index);
            /*if (arrayList.get(index) instanceof Model) {
                return arrayList.get(index);
            } else if(arrayList.get(index) instanceof HashMap) {
                return new Model(arrayList.get(index));
            }
            return null;*/
        } else {
            return null;
        }
    }

    public int size() {
        return arrayList.size();
    }

    public Model set(int index, Model model) {
        return arrayList.set(index, model);
    }

    public int indexOf(Model model) {
        return arrayList.indexOf(model);
    }

    public int lastIndexOf(Model model) {
        return arrayList.lastIndexOf(model);
    }

    public Model remove(int index) {
        return arrayList.remove(index);
    }

    public void clear() {
        arrayList.clear();
    }

    /**
     * Should be overwritten
     *
     * @param url What to download?
     */
    public void load(String url) {
        fire(url);
    }

    /**
     * Start the async download and processing of json
     *
     * @param url
     */
    protected void fire(String url) {
        if (_fetchTask != null) onLoaded(false);

        // Fetch async
        errorMessage = null;
        _fetchTask = new FetchTask();
        _fetchTask.execute(url);
    }

    /* Try to restore by an provided arrayList */

    /**
     * Store an inputList in
     *
     * @param inputList
     */
    public void restore(ArrayList<Model> inputList) {
        if (inputList == null) {
            onLoaded(false);
            return;
        }

        // Clear old items
        this.arrayList.clear();

        //
        if (inputList.size() == 0) {
            onLoaded(true);
            return;
        }

        // Check consistency -- convert to Models if needed..
        // Sometimes on instanceRestore Models are converted to HashMaps. Don't know why..
        if (inputList.get(0) instanceof HashMap) {
            for (HashMap<String, Object> entry : inputList) {
                this.arrayList.add(new Model(entry));
            }
            onLoaded(true);
            return;
        }

        // Everything's the way it should be..
        if (inputList.get(0) instanceof Model) {
            this.arrayList.addAll(inputList);
            onLoaded(true);
            return;
        }

        // Something's wrong here. Return no success..
        onLoaded(false);
    }

    /**
     * Represents an asynchronous task used to load json data from a server
     */
    public class FetchTask extends AsyncTask<String, Void, Boolean> {
        @Override
        protected Boolean doInBackground(String... urls) {
            return fetchJson(urls[0]);
        }

        @Override
        protected void onPostExecute(final Boolean success) {
            // Reset variables
            _fetchTask = null;

            // Trigger onLoaded event
            onLoaded(success);
        }

        @Override
        protected void onCancelled() {
            _fetchTask = null;
        }
    }

    /**
     * Returns the current progress of loading
     *
     * @return Loading: true or not!
     */
    public Boolean isLoading() {
        return _fetchTask != null;
    }

    /**
     * Fetches and stores Json from an Url in our User object
     *
     * @param url Url to the Json
     * @return Success of fetching
     */
    protected Boolean fetchJson(String url) {
        // Reset
        clear();

        // Download Json
        String jsonString = Common.downloadString(url);
	    if (jsonString == null) return false;

        // Try to detect error
        try {
            // Convert json
            JSONObject jsonObject = new JSONObject(jsonString);

            // Error message supplied?
            if (jsonObject.has("error")) {
                errorMessage = jsonObject.getString("error");
                return false;
            }

            // Success field supplied?
            if (jsonObject.has("success")) {
                // What success?
                Boolean success = Boolean.valueOf(jsonObject.getString("success"));

                // No success?
                if (!success) {
                    return false;
                }
            }
        } catch (Exception e) {
            // Do nothing
        }

        // Generate ColumnsMap to create Model
        HashMap<String, String> columsMap = createModelMap();

        // Detect whether its a JSONObject or a JSONArray
        try {
            // Try to parse Json with JSONTokener
            Object json = new JSONTokener(jsonString).nextValue();

            // Try to detect Json Type
            if (json instanceof JSONObject) { // Hey! It's an Object
                // Convert json
                Model record = new Model(columsMap, (JSONObject) json);

                // Add Json to list
                arrayList.add(record);
            } else if (json instanceof JSONArray) { // Hello Array. :)
                // Convert json
                JSONArray jsonArray = (JSONArray) json;

                // Add all entries of the array to our arrayList
                int size = jsonArray.length();
                for (int i = 0; i < size; i++) {
                    // Convert json
                    JSONObject jsonObject = jsonArray.getJSONObject(i);
                    Model record = new Model(columsMap, jsonObject);

                    // Add to list
                    arrayList.add(record);
                }
            }
        } catch (Exception e) {
            return false;
        }
        return true;
    }

    /**
     * Get a model to the store
     */
    abstract public String[][] getModel();

    /**
     * Convert the provided Array of Strings to a Hashmap Array of Strings for faster access
     *
     * @return Converted Array
     */
    private HashMap<String, String> createModelMap() {
        HashMap<String, String> columnsMap = new HashMap<>();

        for (String[] column : getModel()) {
            columnsMap.put(column[0], column[1]);
        }

        return columnsMap;
    }

    /**
     * Called after the loading has finished
     *
     * @param success Success of loading
     */
    public void onLoaded(Boolean success) {
    }

    /**
     * Cancel currently running task
     */
    public void cancelLoading() {
        if (_fetchTask != null) _fetchTask.cancel(true);
        clear();
    }
}