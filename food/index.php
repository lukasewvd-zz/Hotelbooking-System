<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Hotel Booking System</title>
        <!-- demo stylesheet -->
    	<link type="text/css" rel="stylesheet" href="media/layout.css" />

	<!-- helper libraries -->
	<script src="js/jquery/jquery-1.9.1.min.js" type="text/javascript"></script>

	<!-- daypilot libraries -->
        <script src="js/daypilot/daypilot-all.min.js" type="text/javascript"></script>

        <link type="text/css" rel="stylesheet" href="icons/style.css" />

        <style type="text/css">


            .scheduler_default_rowheader_inner
            {
                    border-right: 1px solid #ccc;
            }
            .scheduler_default_rowheadercol2
            {
                background: White;
            }
            .scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner
            {
                top: 2px;
                bottom: 2px;
                left: 2px;
                background-color: transparent;
                    border-left: 5px solid #1a9d13; /* green */
                    border-right: 0px none;
            }
            .status_dirty.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner
            {
                    border-left: 5px solid #ea3624; /* red */
            }
            .status_cleanup.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner
            {
                    border-left: 5px solid #f9ba25; /* orange */
            }
            
            table, th, td {
                border: 1px solid black;
                border-collapse: collapse;
            }
            
            th, td {
                padding: 5px;
            }     

        </style>

    </head>
    <body>
            <div id="header">
                <div class="bg-help">
                    <div class="inBox">
                        <h1 id="logo">Hotel Booking System</h1>
                        <h2 id="claim">Dinner Reservations</h2>
                        <hr class="hidden" />
                    </div>
                </div>
            </div>
            <div class="shadow"></div>
            <div class="hideSkipLink">
            </div>
            <div class="main">

                <div style="width:160px; float:left;">
                    <div id="nav"></div>
                </div>

                <div style="margin-left: 160px;">
                    <a href="/booking/index.php">Room Reservations</a>&nbsp;&nbsp;&nbsp;<a href="#">Dinner Reservations</a>
                    <div class="space">
                        Show rooms:
                        <select id="filter">
                            <option value="0">All</option>
                            <option value="1">1 Bed</option>
                            <option value="2">2 Beds</option>
                            <option value="3">3 Beds</option>
                            <option value="4">4 Beds</option>
                            <option value="6">Cabine</option>
                        </select>

                        <div class="space">
                            Time range:
                            <select id="timerange">
                                <option value="week">Week</option>
                                <option value="month" selected>Month</option>
                            </select>
                            <label for="autocellwidth"><input type="checkbox" id="autocellwidth">Auto Cell Width</label>
                        </div>
                    </div>

                    <div id="dp"></div>

                    <div class="space">
                        <a href="#" id="add-room">Add Room</a>
                    </div>

                </div>

                <script type="text/javascript">
                    var nav = new DayPilot.Navigator("nav");
                    nav.selectMode = "month";
                    nav.showMonths = 3;
                    nav.skipMonths = 3;
                    nav.onTimeRangeSelected = function(args) {
                        loadTimeline(args.start);
                        loadEvents();
                    };
                    nav.init();

                    $("#timerange").change(function() {
                        switch (this.value) {
                            case "week":
                                dp.days = 7;
                                nav.selectMode = "Week";
                                nav.select(nav.selectionDay);
                                break;
                            case "month":
                                dp.days = dp.startDate.daysInMonth();
                                nav.selectMode = "Month";
                                nav.select(nav.selectionDay);
                                break;
                        }
                    });

                    $("#autocellwidth").click(function() {
                        dp.cellWidth = 40;  // reset for "Fixed" mode
                        dp.cellWidthSpec = $(this).is(":checked") ? "Auto" : "Fixed";
                        dp.update();
                    });

                    $("#add-room").click(function(ev) {
                        ev.preventDefault();
                        var modal = new DayPilot.Modal();
                        modal.onClosed = function(args) {
                            loadResources();
                        };
                        modal.showUrl("room_new.php");
                    });
                </script>

                <script>
                    var dp = new DayPilot.Scheduler("dp");

                    dp.allowEventOverlap = false;

                    //dp.scale = "Day";
                    //dp.startDate = new DayPilot.Date().firstDayOfMonth();
                    dp.days = dp.startDate.daysInMonth();
                    loadTimeline(DayPilot.Date.today().firstDayOfMonth());

                    dp.eventDeleteHandling = "Update";

                    dp.timeHeaders = [
                        { groupBy: "Month", format: "MMMM yyyy" },
                        { groupBy: "Day", format: "d" }
                    ];

                    dp.eventHeight = 50;
                    dp.bubble = new DayPilot.Bubble({});

                    dp.rowHeaderColumns = [
                        {title: "Room", width: 80},
                        {title: "Capacity", width: 80},
                    ];

                    dp.onBeforeResHeaderRender = function(args) {
                        var beds = function(count) {
                            return count + " bed" + (count > 1 ? "s" : "");
                        };

                        args.resource.columns[0].html = beds(args.resource.capacity);
                        args.resource.columns[1].html = args.resource.status;

                        args.resource.areas = [{
                                    top:3,
                                    right:4,
                                    height:14,
                                    width:14,
                                    action:"JavaScript",
                                    js: function(r) {
                                        var modal = new DayPilot.Modal();
                                        modal.onClosed = function(args) {
                                            loadResources();
                                        };
                                        modal.showUrl("room_edit.php?id=" + r.id);
                                    },
                                    v:"Hover",
                                    css:"icon icon-edit",
                                }];
                    };

                    // http://api.daypilot.org/daypilot-scheduler-oneventmoved/
                    dp.onEventMoved = function (args) {
                        $.post("backend_move.php",
                        {
                            id: args.e.id(),
                            newStart: args.newStart.toString().slice(0, -9),
                            newEnd: args.newEnd.toString().slice(0, -9),
                            newResource: args.newResource
                        },
                        function(data) {
                            dp.message(data.message);
                        });
                    };

                    // http://api.daypilot.org/daypilot-scheduler-oneventresized/
                    dp.onEventResized = function (args) {
                        $.post("backend_resize.php",
                        {
                            id: args.e.id(),
                            newStart: args.newStart.toString(),
                            newEnd: args.newEnd.toString()
                        },
                        function() {
                            dp.message("Resized.");
                        });
                    };

                    dp.onEventDeleted = function(args) {
                        $.post("backend_delete.php",
                        {
                            id: args.e.id()
                        },
                        function() {
                            dp.message("Deleted.");
                        });
                    };

                    // event creating
                    // http://api.daypilot.org/daypilot-scheduler-ontimerangeselected/
                    dp.onTimeRangeSelected = function (args) {
                        //var name = prompt("New event name:", "Event");
                        //if (!name) return;

                        var modal = new DayPilot.Modal();
                        modal.closed = function() {
                            dp.clearSelection();

                            // reload all events
                            var data = this.result;
                            if (data && data.result === "OK") {
                                loadEvents();
                            }
                        };
                        modal.showUrl("new.php?start=" + args.start + "&end=" + args.end + "&resource=" + args.resource);

                    };

                    dp.onEventClick = function(args) {
                        var modal = new DayPilot.Modal();
                        modal.closed = function() {
                            // reload all events
                            var data = this.result;
                            if (data && data.result === "OK") {
                                loadEvents();
                            }
                        };
                        modal.showUrl("edit.php?id=" + args.e.id());
                    };

                    dp.onBeforeCellRender = function(args) {
                        var dayOfWeek = args.cell.start.getDayOfWeek();
                        if (dayOfWeek === 6 || dayOfWeek === 0) {
                            args.cell.backColor = "#f8f8f8";
                        }
                    };

                    dp.onBeforeEventRender = function(args) {
                        var start = new DayPilot.Date(args.e.start);
                        var end = new DayPilot.Date(args.e.end);

                        var today = new DayPilot.Date().getDatePart();

                        args.e.html = args.e.text;

                        switch (args.e.status) {
                            case "Normal":
                                args.e.barColor = 'green';
                                args.e.toolTip = "Normal";
                                break;
                            case "Special":
                                args.e.barColor = 'red';
                                args.e.toolTip = "Special";
                                break;
                            default:
                                args.e.toolTip = "Unexpected state";
                                break;
                        }
                    };


                    dp.init();

                    loadResources();
                    loadEvents();

                    function loadTimeline(date) {
                        dp.scale = "Manual";
                        dp.timeline = [];
                        var start = date.getDatePart();

                        for (var i = 0; i < dp.days; i++) {
                            dp.timeline.push({start: start.addDays(i), end: start.addDays(i+1)});
                        }
                        dp.update();
                    }

                    function loadEvents() {
                        var start = dp.visibleStart();
                        var end = dp.visibleEnd();

                        $.post("backend_events.php",
                            {
                                start: start.toString(),
                                end: end.toString()
                            },
                            function(data) {
                                dp.events.list = data;
                                dp.update();
                            }
                        );
                    }

                    function loadResources() {
                        $.post("backend_rooms.php",
                        { capacity: $("#filter").val() },
                        function(data) {
                            dp.resources = data;
                            dp.update();
                        });
                    }

                    $(document).ready(function() {
                        $("#filter").change(function() {
                            loadResources();
                        });
                    });

                </script>
                <?php
                    $dbhandle = new PDO('sqlite:foodDB.sqlite');
                    $sqlTotal = $dbhandle->prepare("SELECT SUM(amtStarter), SUM(amtMain), SUM(amtDessert), note 
                    FROM Reservations WHERE date('now') >= start AND date('now') < end;");
                    $sqlTotal->execute();
                    $totalResult = $sqlTotal->fetch(PDO::FETCH_NUM);
                    $amtStarter = $totalResult[0];
                    $amtMain = $totalResult[1];
                    $amtDessert = $totalResult[2];
                ?>
                <?php
                    $dbhandle = new PDO('sqlite:foodDB.sqlite');
                    $sqlTotal = $dbhandle->prepare("SELECT group_concat(DISTINCT note)
                                                    FROM Reservations
                                                    WHERE date('now') >= start AND date('now') < end;");
                    $sqlTotal->execute();
                    $totalResult = $sqlTotal->fetch(PDO::FETCH_NUM);
                    $note = $totalResult[0];
                    //$note = substr($note, 1);
                    $note = str_replace(",", "<br>", $note);
                ?>
                <?php
                    $dbhandle = new PDO('sqlite:foodDB.sqlite');
                    $sqlTotal = $dbhandle->prepare("SELECT SUM(amtStarter), SUM(amtMain), SUM(amtDessert), note 
                    FROM Reservations WHERE date('now' , '+1 days') >= start AND date('now', '+1 days') < end;");
                    $sqlTotal->execute();
                    $totalResult = $sqlTotal->fetch(PDO::FETCH_NUM);
                    $amtStarterT = $totalResult[0];
                    $amtMainT = $totalResult[1];
                    $amtDessertT = $totalResult[2];
                ?>
                <?php
                    $dbhandle = new PDO('sqlite:foodDB.sqlite');
                    $sqlTotal = $dbhandle->prepare("SELECT group_concat(DISTINCT note)
                                                    FROM Reservations
                                                    WHERE date('now', '+1 days') >= start AND date('now', '+1 days') < end;");
                    $sqlTotal->execute();
                    $totalResult = $sqlTotal->fetch(PDO::FETCH_NUM);
                    $noteT = $totalResult[0];
                    //$noteT = substr($noteT, 1);
                    $noteT = str_replace(",", "<br>", $noteT);
                ?>
                <table style="width:75%">
                    <tr>
                        <td><h2 style="font-weight:lighter;">Reservations: </h2></td>
                        <th><h2 style="font-weight:lighter;">Today</th>
                        <th><h2 style="font-weight:lighter;">Tomorrow</th>
                    </tr>
                    <tr>
                        <td><h3 style="font-weight:lighter;">Amount of Starters</td>
                        <th><h3><?php print $amtStarter ?></th>
                        <th><h3><?php print $amtStarterT ?></th>
                    </tr>
                    <tr>
                        <td><h3 style="font-weight:lighter;">Amount of Main</td>
                        <th><h3><?php print $amtMain ?></th>
                        <th><h3><?php print $amtMainT ?></th>
                    </tr>
                    <tr>
                        <td><h3 style="font-weight:lighter;">Amount of Dessert</td>
                        <th><h3><?php print $amtDessert ?></th>
                        <th><h3><?php print $amtDessertT ?></th>
                    </tr>
                    <tr>
                        <td><h3 style="font-weight:lighter;">Notes</td>
                        <th><h3 style="font-weight:lighter;"><?php print $note ?></th>
                        <th><h3 style="font-weight:lighter;"><?php print $noteT ?></th>
                    </tr>
                </table>
            </div>
            <div class="clear">
            </div>
    </body>
</html>
