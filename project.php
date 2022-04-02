<!--Test Oracle file for UBC CPSC304 2018 Winter Term 1
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22)
  This file shows the very basics of how to execute PHP commands
  on Oracle.
  Specifically, it will drop a table, create a table, insert values
  update values, and then query for values
  IF YOU HAVE A TABLE CALLED "demoTable" IT WILL BE DESTROYED
  The script assumes you already have a server set up
  All OCI commands are commands to the Oracle libraries
  To get the file to work, you must place it somewhere where your
  Apache server can run it, and you must rename it to have a ".php"
  extension.  You must also change the username and password on the
  OCILogon below to be your ORACLE username and password -->

  <html>
    <head>
        <title>CPSC 304 PHP/Oracle Demonstration</title>
    </head>

    <body>
        <h2>Reset</h2>
        <p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>

        <form method="POST" action="project.php">
            <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
            <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
            <p><input type="submit" value="Reset" name="reset"></p>
        </form>

        <hr />

        <h2>Initiate Table</h2>
        <p>Populate the tables</p>

        <form method="POST" action="project.php">
            <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
            <input type="hidden" id="initTableQueryRequest" name="initTableQueryRequest">
            <p><input type="submit" value="Populate" name="populate"></p>
        </form>

        <hr />


        <h2>Insert review </h2>
        <form method="POST" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            ID: <input type="text" name="review_id"> <br /><br />
            Score: <input type="text" name="num_star"> <br /><br />
            Restaurant ID: <input type="text" name="rid"> <br /><br />
            <p><input type="submit" value="Insert" name="insertSubmit"></p>
        </form>

    

        <hr />
        <h2>Update num_star in FiveStarReview</h2>
        <p>The value must be between 1 and 5 inclusive, any other value will be rejected</p>

        <form method="POST" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
           Review ID: <input type="text" name="reviewid"> <br /><br />
            New Name: <input type="number" name="newrating"> <br /><br />

             <p> <input type="submit" value="Update" name="updateSubmit"></p>
        </form>
 <hr />

        <h2>Delete Review</h2>
        <p>If the rid does not exist query will be rejected</p>

        <form method="POST" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="deleteQueryRequest" name="deleteQueryRequest">
            Review ID: <input type="text" name="reviewid"> <br /><br />

            <p> <input type="submit" value="Delete" name="deleteSubmit"></p>
        </form>
 <hr />


<h2>Selection</h2>
        <p>Get all owners with networth greater than entered value </p>

        <form method="GET" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="selectQueryRequest" name="selectQueryRequest">
            Review ID: <input type="number" name="networth"> <br /><br />

            <input type="submit" value="GetOwners" name="selectionSubmit"></p>
        </form>


        <hr />

<h2>Projection</h2>
        <p>Get the specified column from specified relation</p>

        <form method="GET" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="projectQueryRequest" name="projectQueryRequest">
            Relation: <input type="text" name="relation"> <br /><br />
	        Column: <input type="text" name="column"> <br /><br />

            <input type="submit" value="GetColumn" name="projectionSubmit"></p>
        </form>


        <hr />


<h2>Join</h2>
        <p>Get all the restuarants in a certain city</p>

        <form method="GET" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="joinQueryRequest" name="joinQueryRequest">
            City: <input type="text" name="city"> <br /><br />

            <input type="submit" value="GetRestaurants" name="joinSubmit"></p>
        </form>


        <hr />

        <h2>Count the Tuples in DemoTable</h2>
        <form method="GET" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="countTupleRequest" name="countTupleRequest">
            <input type="submit" name="countTuples"></p>
        </form>

        <h2>Avg the Tuples in DemoTable [Aggregation Query]</h2>
        <form method="GET" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="getAvgRequest" name="getAvgRequest">
            <input type="submit" name="getAvg"></p>
        </form>

        <h2>Max Avg the Tuples in DemoTable [Nested-Aggregation Query]</h2>
        <form method="GET" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="getMaxAvgRequest" name="getMaxAvgRequest">
            <input type="submit" name="getMaxAvg"></p>
        </form>

        <h2>Search for restaurant with 0-5 stars point [Div Query]</h2>
        <form method="GET" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="getDivRequest" name="getDivRequest">
            <input type="submit" name="getDiv"></p>
        </form>

        <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            //echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }

			return $statement;
		}

        function executeBoundSQL($cmdstr, $list) {
            /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

			global $db_conn, $success;
			$statement = OCIParse($db_conn, $cmdstr);

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn);
                echo htmlentities($e['message']);
                $success = False;
            }

            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    //echo $val;
                    //echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
				}

                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                    $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                    echo htmlentities($e['message']);
                    echo "<br>";
                    $success = False;
                }
            }
        }

        function printResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from table demoTable:<br>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]"
            }

            echo "</table>";
        }

        function connectToDB() {
            global $db_conn;
            
            // Your username is ora_(CWL_ID) and the password is a(student number). For example,
			// ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_dgupta10", "a21276829", 
"dbhost.students.cs.ubc.ca:1522/stu");

            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }
        }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

        function handleUpdateRequest() {
            global $db_conn;

            $review_id = $_POST['reviewid'];
            $new_rating = $_POST['newrating'];

            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("UPDATE review SET numstar ='" . $new_rating . "' WHERE review_id ='" . $review_id . "'");
            OCICommit($db_conn);
        }

  function handleDeleteRequest() {
            global $db_conn;
            $review_id = $_POST['reviewid'];

            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("DELETE FROM review WHERE review_id ='" . $review_id . "'");
            OCICommit($db_conn);
        }

        function handleResetRequest() {
            global $db_conn;
            // Drop old table
            executePlainSQL("DROP TABLE review");
            executePlainSQL("DROP TABLE Restaurant");
            executePlainSQL("DROP TABLE Owner");
            executePlainSQL("DROP TABLE Ingredient");
            executePlainSQL("DROP TABLE LocationTable cascade constraints");

            // Create new table
            echo "<br> creating new table <br>";
             executePlainSQL("CREATE TABLE LocationTable( 
                latitude float , 
                longitude float ,
                address char(40),
                postal_code char(20),
                city char(20),
                CONSTRAINT supplier_pk PRIMARY KEY (latitude,longitude)
                    )");
            executePlainSQL("CREATE TABLE Restaurant( 
                              rid int PRIMARY KEY, 
                              rname char(30),
                              latitude float NOT NULL,
                              longitude float NOT NULL,
                              CONSTRAINT fk_supplier_comp
                                FOREIGN KEY (latitude,longitude)
                                REFERENCES LocationTable(latitude,longitude))");

            executePlainSQL("CREATE TABLE review (review_id int PRIMARY KEY, 
                                numstar INTEGER check((numstar >=  0) and (numstar <=5)) , 
                                rid int, 
                            CONSTRAINT fk_supplier_comp1
                                FOREIGN KEY (rid)
                                REFERENCES Restaurant(rid))
                            ");

             
            executePlainSQL("CREATE TABLE Owner(owner_id int PRIMARY KEY, networth_cad int, name char(30) NOT NULL)");
            executePlainSQL("CREATE TABLE Ingredient(ingredient_id int PRIMARY KEY, name char(30) NOT NULL)");
            

            OCICommit($db_conn);
        }

        function handleInitRequest() {
             global $db_conn;


            // Owner: owner_id, networth_cad, name
            executePlainSQL("INSERT INTO owner
                        VALUES ('001', '40000', 'owner 1')");
            executePlainSQL("INSERT INTO owner
                        VALUES ('002', '50000', 'owner 2')");
            executePlainSQL("INSERT INTO owner
                        VALUES ('003', '45000', 'owner 3')");
            executePlainSQL("INSERT INTO owner
                        VALUES ('004', '44000', 'owner 4')");
            // Ingredient: ingredient_id, name
            executePlainSQL("INSERT INTO ingredient
                        VALUES ('101', 'ingredient 1')");
            executePlainSQL("INSERT INTO ingredient
                        VALUES ('102', 'ingredient 2')");
            executePlainSQL("INSERT INTO ingredient
                        VALUES ('103', 'ingredient 3')");
            executePlainSQL("INSERT INTO ingredient
                        VALUES ('104', 'ingredient 4')");
            executePlainSQL("INSERT INTO ingredient
                        VALUES ('105', 'ingredient 5')");
            executePlainSQL("INSERT INTO ingredient
                        VALUES ('106', 'ingredient 6')");
            // postalCity: postal_code, city
            
            // LocationTable: lat, lon, add , pos, city
            executePlainSQL("INSERT INTO LocationTable 
                        VALUES ('10', '115', '453-ABC vv','A2A A2V', 'city 1')");
            executePlainSQL("INSERT INTO LocationTable 
                        VALUES ('11', '114', '454-ABC vv','A2A B2V', 'city 1' )");
            executePlainSQL("INSERT INTO LocationTable 
                        VALUES ('12', '113', '455-ABC vv','A2A C2V', 'city 3' )");
            executePlainSQL("INSERT INTO LocationTable 
                        VALUES ('13', '112', '456-ABC vv','A2A D2V', 'city 4' )");
            executePlainSQL("INSERT INTO LocationTable 
                        VALUES ('14', '111', '457-ABC vv','A2A E2V', 'city 5' )");

            executePlainSQL("INSERT INTO Restaurant 
                        VALUES ('15', 'mcdonald', 10 , 115)");
            executePlainSQL("INSERT INTO Restaurant 
                        VALUES ('24', 'pizza hut', 11, 114)");

            executePlainSQL("INSERT INTO Restaurant 
                        VALUES ('34', 'dominos', 12, 113 )");

            executePlainSQL("INSERT INTO Restaurant 
                        VALUES ('45', 'wendys', 14, 111)");

            executePlainSQL("INSERT INTO Review 
                        VALUES (11561, 3, 15)"); 
            executePlainSQL("INSERT INTO Review 
             VALUES (1, 5, 15)"); 
            executePlainSQL("INSERT INTO Review 
            VALUES (111, 2, 24)");
            executePlainSQL("INSERT INTO Review 
            VALUES (213, 3, 34)"); 
            executePlainSQL("INSERT INTO Review 
            VALUES (221, 3, 45)"); 
            OCICommit($db_conn);
        }

        function handleGetAvgRequest() {
            global $db_conn;
            echo "<br> The avg star in review: <br>";
            $result = executePlainSQL("SELECT rname, review.rid, AVG(numstar)
                                        FROM review
                                        INNER JOIN Restaurant 
                                        ON review.rid = Restaurant.rid
                                        GROUP BY review.rid, rname
                                        ORDER BY AVG(numstar) DESC
                                        ");
            echo "<br> The avg star in each restaurant review: <br>";
            while (($row = oci_fetch_row($result)) != false) {
                echo "   name : " . $row[0]  . " average : " . $row[2] . "<br>";

            }
        }

        function handleGetMaxAvgRequest() {
            global $db_conn;
            echo "<br> The top-rated Restaurant is<br>";
            $result = executePlainSQL("SELECT rname, numstar
                                        FROM review
                                        INNER JOIN Restaurant 
                                        ON review.rid = Restaurant.rid and review.numstar = (SELECT MAX(avg) FROM (SELECT rname as r_name, review.rid, AVG(numstar) as avg
                                                                                                FROM review
                                                                                                INNER JOIN Restaurant 
                                                                                                ON review.rid = Restaurant.rid
                                                                                                GROUP BY review.rid, rname
                                                                                                ORDER BY AVG(numstar) DESC)
                                        )
                                        ");
            while (($row = oci_fetch_row($result)) != false) {
                echo "   name : " . $row[0]  . " average : " . $row[1] . "<br>";

            }
        }

        function handleGetDivRequest() {
            global $db_conn;
            //create table with number 0-5
            executePlainSQL("DROP TABLE nums");
            executePlainSQL("CREATE TABLE nums( 
                num int )");
            executePlainSQL("INSERT INTO nums VALUES ('1')");
            executePlainSQL("INSERT INTO nums VALUES ('2')");
            executePlainSQL("INSERT INTO nums VALUES ('3')");
            executePlainSQL("INSERT INTO nums VALUES ('4')");
            executePlainSQL("INSERT INTO nums VALUES ('5')");


            $result = executePlainSQL("SELECT rname 
                                        FROM Restaurant r1
                                        WHERE NOT EXISTS ((SELECT num from nums)
                                                            MINUS
                                                            (SELECT review.numstar
                                                            FROM review
                                                            INNER JOIN Restaurant 
                                                            ON review.rid = Restaurant.rid and review.rid = r1.rid
                                                            ))
                                        
                                        ");
            echo "<br> The restaurant that have every star rating 1-5: <br>";
            while (($row = oci_fetch_row($result)) != false) {
                echo "   name : " . $row[0]  ."<br>";

            }
        }  

  function handleInsertRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            
              $tuple = array (
                ":bind1" => $_POST['review_id'],
                ":bind2" => $_POST['num_star'],
                ":bind3" => $_POST['rid']
            );

            $alltuples = array (
                $tuple
            );

            // echo "t " + $_POST;
            executeBoundSQL("insert into review values (:bind1, :bind2, :bind3)", $alltuples);
            OCICommit($db_conn);
        }

        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM demoTable");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
            }
        }

        function handleProjectionRequest() {
            global $db_conn;
            $rel = $_GET['relation'];
            $col = $_GET['column'];
            $string = "select " . $col . " from " . $rel;
        
            $result = executePlainSQL($string);

            while (($row = oci_fetch_row($result)) != false) {
            echo "<br> Result " . $row[0] . "<br>"; }
        }

        function handleSelectRequest() {
            global $db_conn;
            $nw = $_GET['networth'];
            $string = "select name, owner_id  from owner where networth_cad >= " . $nw;
        
            $result = executePlainSQL($string);

            while (($row = oci_fetch_row($result)) != false) {
            echo "<br> name " . $row[0] . " owner_id : " . $row[1] .  "<br>"; } }


        function handleJoinRequest() {
            global $db_conn;
            $city = $_GET['city'];
            $string = "select r.rid, r.rname from restaurant r, locationtable l where l.city = '{$city}'  and l.latitude = r.latitude and l.longitude = r.longitude";
        
            $result = executePlainSQL($string);

            while (($row = oci_fetch_row($result)) != false) {
            echo "<br> restaurant_id " . $row[0] . " restaurant_name : " . $row[1] .  "<br>"; } }
            

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            echo "+ ". $_POST . " asd";
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
                } else if (array_key_exists('deleteQueryRequest', $_POST)) {
                    handleDeleteRequest();
                }else if (array_key_exists('initTableQueryRequest', $_POST)) {
                    echo "<br> The number of    <br>";
                    handleInitRequest(array_key_exists('initTableQueryRequest', $_POST));
                }

                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countTuples', $_GET)) {
                    handleCountRequest();
                } else if (array_key_exists('getAvg', $_GET)) {
                    handleGetAvgRequest();
                } else if (array_key_exists('getMaxAvg', $_GET)) {
                    handleGetMaxAvgRequest();
                } else if (array_key_exists('getDiv', $_GET)) {
                    handleGetDivRequest();
                } else if (array_key_exists('selectionSubmit', $_GET)) {
                    handleSelectRequest();
                } else if (array_key_exists('projectionSubmit', $_GET)) {
                    handleProjectionRequest();
                } else if (array_key_exists('joinSubmit', $_GET)) {
                    handleJoinRequest();
                }


                disconnectFromDB();
            }
        }

		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit']) ||  isset($_POST['populate']) || isset($_POST['deleteSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest']) || isset($_GET['getAvgRequest']) || isset($_GET['getMaxAvgRequest'])|| isset($_GET['getDivRequest'])
        || isset($_GET['selectQueryRequest']) || isset($_GET['projectQueryRequest']) || isset($_GET['joinQueryRequest'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>
