/*
 * Copyright (c) Konrad Kollnig 2015.
 */

package com.ops.app.libs;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.Serializable;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashMap;
import java.util.Iterator;

public class Model extends HashMap<String, Object> implements Serializable {
	/**
	 * Make sure to assign a unique serialVersionUID
	 */
	private static final long serialVersionUID = 2L;

	protected String _dateFormat = "yyyy-MM-dd";

	// Cast HashMap to Model
	public Model (HashMap<String, Object> model) {
		this.putAll(model);
	}

    /*public Model (String[][] columns, JSONObject jsonObject) throws JSONException, ParseException {
        this(columns, jsonObject, null);
    }*/

	/**
	 * @param columnsMap Array of columns. To preset columns. Format: "key", "type"
	 *                   <p/>
	 *                   Types:
	 *                   - boolean
	 *                   - date
	 *                   - int
	 *                   - string
	 * @param jsonObject The JSONObject that shall be converted
	 * @throws JSONException
	 * @throws ParseException
	 */
	public Model (HashMap<String, String> columnsMap, JSONObject jsonObject/*, String dateFormat*/) throws JSONException, ParseException {
		// Save values
		//if (dateFormat != null) _dateFormat = dateFormat;
		setDateFormat("yyyy-MM-dd");

		// Loop through JSONObject and add to data
		Iterator<String> iterator = jsonObject.keys();

		while (iterator.hasNext()) {
			String key = iterator.next();

			// Key assigned?
			if (!columnsMap.containsKey(key)) continue;

			// Check type of the key
			switch (columnsMap.get(key)) {
				case ("boolean"): {
					Boolean value = Boolean.valueOf(jsonObject.getString(key));
					this.put(key, value);
					break;
				}
				case ("date"): {
					Date value = new SimpleDateFormat(_dateFormat).parse(jsonObject.getString(key));
					this.put(key, value);
					break;
				}
				case ("int"): {
					int value = jsonObject.getInt(key);
					this.put(key, value);
					break;
				}
				case ("string"): {
					String value = jsonObject.getString(key);
					this.put(key, value);
					break;
				}
			}
		}
	}

	protected void setDateFormat (String dateFormat) {
		_dateFormat = dateFormat;
	}

	/**
	 * Override to skip "has" checks..
	 *
	 * @param key Key of the value to search
	 * @return
	 */
	@Override
	public Object get (Object key) {
		if (this.containsKey(key)) {
			return super.get(key);
		} else {
			return null;
		}
	}

	public String getString (Object key) {
		String value = (String) this.get(key);
		if (value == null || value.equals("null")) return null;

		return value;
	}
}