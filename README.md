Converting this to React
========================

**Make sure to run this with the latest version of Tsugi.  We will be adding APIs
to Tsugi as we go to suport the evolving needs of this application.**

This application has been switched to REST web services for all of its data.
There are two main files:

* `student.php` is the first place to start - It is simple and uses exactly one web service `api/attend.php`

* `index.php` contains the Instructor UI - it is more complex - and includes the settttings capabilities.
Everything except the initial page draw is done with REST web services.

The web services are all in the `api` folder.

Web Services
------------

The web services use standard Tsugi authentication. A `PHPSESSID` must be provided as either
a `GET` or `POST` parameter.   At this point, the Tsugi `requireData` does not yet handle this
as a JSON parameter - but this can be added.

*Recording Attendance (Student)*

The API is within the `rattend` folder.

    POST to `/mod/rattend/api/attend.php?PHPSESSID=4bee80a39723e224f4001b32aed026f6`
    {"code":"Yada"}

Return

    403 if authentication is wrong

    200 if the code works - The JSON includes whether or not attendance was recorded

    {"status":"success","detail":""}
    {"status":"failure","detail":""}

The `detail` field includes a message that can be displayed to a user - for now it
indicates if there is an IP mis-match.

*Getting the Attendance Data (Instructor)*

The API is within the `rattend` folder.

    GET to `/mod/rattend/api/getrows.php?PHPSESSID=4bee80a39723e224f4001b32aed026f6`

Return

    403 if authentication is wrong

    200 returns an array (can be evolved to have object as container)
    [
        {
            "user_id": "5",
            "attend": "2021-07-27",
            "ipaddr": "127.0.0.1",
            "displayname": "Ed Student",
            "email": "ed@ischool.edu"
        }
    ]

*Updating Settings (Instructor)*

This api is in the `tsugi/api` folder (i.e. tsugi-wide APIs)


    POST to `../tsugi/api/settings.php?PHPSESSID=4bee80a39723e224f4001b32aed026f6`
    {
        "PHPSESSID": "82e572fa15cde199f96550cfaabe3c59",
        "code": "1234",
        "grade": "1",
        "match": ""
    }

Note - at this point PHPSESSID in the JSON is not used to authenticate the request - it is
there for future use.

Return

    403 if authentication is wrong

    200 if the settings were updated - empty JSON is returned to make jQuery happy
    { } 

I am happy to get suggestions as to how to make these more "RESTy".






