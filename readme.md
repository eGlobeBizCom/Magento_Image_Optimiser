This is a work in progress, the goal is to make an image optimizer addon for magento.

The magento module adds a cron job which checks all images in the media dir if they can be resized, optimised and smushed.

Allows setting of certain values for your convenience from the configuration menu.

This script however is quite slow with large collections. For this purpose we use a txt file work_list with a list of images in it.

Still to do:

Found 4 TODO items in 1 file
Observer.php
(266, 5) * @todo use an admin value for cron schedule
(267, 5) * @todo clean up the observer file and move classes to their own model
(448, 9) * @todo leave the logging to magento
(467, 9) * @todo reverse logic with skipping files to include only mentioned file formats use magento settings?