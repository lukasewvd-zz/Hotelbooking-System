<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Event</title>
    	<link type="text/css" rel="stylesheet" href="media/layout.css" />
        <script src="js/jquery/jquery-1.9.1.min.js" type="text/javascript"></script>
    </head>
    <body>
        <?php
            // check the input
            is_numeric($_GET['id']) or die("invalid URL");

            require_once '_db.php';

            $stmt = $db->prepare('SELECT * FROM reservations WHERE id = :id');
            $stmt->bindParam(':id', $_GET['id']);
            $stmt->execute();
            $reservation = $stmt->fetch();

            $rooms = $db->query('SELECT * FROM rooms');
        ?>
        <form id="f" action="backend_update.php" style="padding:20px;">
            <input type="hidden" name="id" value="<?php print $_GET['id'] ?>" />
            <h1>Edit Reservation</h1>
            <div>Start:</div>
            <div>
                <?php
                if (strlen($reservation['start']) == 19) {
                    $trimedDateStart = substr($reservation['start'], 0, -9);
                } else {
                    $trimedDateStart = $reservation['start'];
                }
                ?>
                <input type="text" id="start" name="start" value="<?php print $trimedDateStart ?>" />
            </div>
            <div>End:</div>
            <div>
                <?php
                if (strlen($reservation['end']) == 19) {
                    $trimedDateEnd = substr($reservation['end'], 0, -9);
                } else {
                    $trimedDateEnd = $reservation['end'];
                }
                ?>
                <input type="text" id="end" name="end" value="<?php print $trimedDateEnd ?>" />
            </div>
            <div>Room:</div>
            <div>
                <select id="room" name="room">
                    <?php
                        foreach ($rooms as $room) {
                            $selected = $reservation['room_id'] == $room['id'] ? ' selected="selected"' : '';
                            $id = $room['id'];
                            $name = $room['name'];
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>

            </div>
            <div>Name: </div>
            <div><input type="text" id="name" name="name" value="<?php print $reservation['name'] ?>" /></div>
            <div>Starters:</div>
            <div>
                <select id="amtStarter" name="amtStarter">
                    <?php
                        $options = array("0", "1", "2", "3", "4", "5", "6");
                        foreach ($options as $option) {
                            $selected = $option == $reservation['amtStarter'] ? ' selected="selected"' : '';
                            $id = $option;
                            $name = $option;
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>
            </div>
            <div>Main Dish:</div>
            <div>
                <select id="amtMain" name="amtMain">
                    <?php
                        $options = array("0", "1", "2", "3", "4", "5", "6");
                        foreach ($options as $option) {
                            $selected = $option == $reservation['amtMain'] ? ' selected="selected"' : '';
                            $id = $option;
                            $name = $option;
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>
            </div>
            <div>Desserts:</div>
            <div>
                <select id="amtDessert" name="amtDessert">
                    <?php
                        $options = array("0", "1", "2", "3", "4", "5", "6");
                        foreach ($options as $option) {
                            $selected = $option == $reservation['amtDessert'] ? ' selected="selected"' : '';
                            $id = $option;
                            $name = $option;
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>
            </div>
            <div>Additional note: </div>
            <div><textarea rows="2" cols="50" type="text" id="note" name="note"><?php print $reservation['note'] ?></textarea></div>
            <div>Status:</div>
            <div>
                <select id="status" name="status">
                    <?php
                        $options = array("Normal", "Special");
                        foreach ($options as $option) {
                            $selected = $option == $reservation['status'] ? ' selected="selected"' : '';
                            $id = $option;
                            $name = $option;
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>
            </div>
            <div>Paid:</div>
            <div>
                <select id="paid" name="paid">
                    <?php
                        $options = array("No", "Yes");
                        foreach ($options as $option) {
                            $selected = $option == $reservation['paid'] ? ' selected="selected"' : '';
                            $id = $option;
                            $name = $option."";
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
