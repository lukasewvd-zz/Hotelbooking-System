<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>New Reservation</title>
    	<link type="text/css" rel="stylesheet" href="media/layout.css" />
        <script src="js/jquery/jquery-1.9.1.min.js" type="text/javascript"></script>
    </head>
    <body>
        <?php
            // check the input
            //is_numeric($_GET['id']) or die("invalid URL");

            require_once '_db.php';

            $rooms = $db->query('SELECT * FROM rooms');

            $start = $_GET['start']; // TODO parse and format
            $end = $_GET['end']; // TODO parse and format
        ?>
        <form id="f" action="backend_create.php" style="padding:20px;">
            <h1>New Reservation</h1>
            <div>Name: </div>
            <div><input type="text" id="name" name="name" value="" /></div>
            <div>Phone number: </div>
            <div><input type="text" id="phoneNumber" name="phoneNumber" value="" /></div>
            <div>Adults:</div>
            <div>
                <select id="amtAdults" name="amtAdults">
                    <?php
                        $options = array("0", "1", "2", "3", "4", "5", "6");
                        foreach ($options as $option) {
                            $selected = $option == $reservation['amtAdults'] ? ' selected="selected"' : '';
                            $id = $option;
                            $name = $option;
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>
            </div>
            <div>Youth (13-18):</div>
            <div>
                <select id="amtYouth" name="amtYouth">
                    <?php
                        $options = array("0", "1", "2", "3", "4", "5", "6");
                        foreach ($options as $option) {
                            $selected = $option == $reservation['amtYouth'] ? ' selected="selected"' : '';
                            $id = $option;
                            $name = $option;
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>
            </div>
            <div>Children (0-12):</div>
            <div>
                <select id="amtChildren" name="amtChildren">
                    <?php
                        $options = array("0", "1", "2", "3", "4", "5", "6");
                        foreach ($options as $option) {
                            $selected = $option == $reservation['amtChildren'] ? ' selected="selected"' : '';
                            $id = $option;
                            $name = $option;
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>
            </div>
            <div>Additional note: </div>
            <div><textarea rows="4" cols="50" type="text" id="note" name="note"></textarea></div>
            <div>Start:</div>
            <div>
                <input type="text" id="start" name="start" value="<?php echo $start ?>" /></div>
            <div>End:</div>
            <div><input type="text" id="end" name="end" value="<?php echo $end ?>" /></div>
            <div>Room:</div>
            <div>
                <select id="room" name="room">
                    <?php
                        foreach ($rooms as $room) {
                            $selected = $_GET['resource'] == $room['id'] ? ' selected="selected"' : '';
                            $id = $room['id'];
                            $name = $room['name'];
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="space"><input type="submit" value="Save" /> <a href="javascript:close();">Cancel</a></div>
        </form>

        <script type="text/javascript">
        function close(result) {
            if (parent && parent.DayPilot && parent.DayPilot.ModalStatic) {
                parent.DayPilot.ModalStatic.close(result);
            }
        }

        $("#f").submit(function () {
            var f = $("#f");
            $.post(f.attr("action"), f.serialize(), function (result) {
                close(eval(result));
            });
            return false;
        });

        $(document).ready(function () {
            $("#name").focus();
        });

        </script>
    </body>
</html>
