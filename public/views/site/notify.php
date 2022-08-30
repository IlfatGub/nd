<?php
/**
 * Created by PhpStorm.
 * User: 01gig
 * Date: 25.10.2019
 * Time: 10:27
 */

use app\models\Calendar;

use app\modules\admin\models\Login;

 ?>




<?= $this->render('_notify', [
    'model' => $model,
]) ?>



<div class="col-12">
 <?php
 \app\models\AppNotify::getAdminNotify();
 ?>



<!-- --><?php
// $invite = new Calendar();
// $invite
//     ->setSubject("Test Demo Invite")
//     ->setDescription("The is a test invite for you to see how this thing actually works")
//     ->setStart(new DateTime('2012-05-10 01:00PM EDT'))
//     ->setEnd(new DateTime('2012-05-10 02:00PM EDT'))
//     ->setLocation("Queens, New York")
//     ->setOrganizer("john@doe.com", "John Doe")
//     ->addAttendee("ahmad@ahmadamin.com", "Ahmad Amin")
//     ->generate() // generate the invite
//     ->save(); // save it to a file
// // as you may notice this is a static method
// // it is indipendent of the object.
// $download_link = Calendar::getSavedPath();
// ?>
<!-- <a href="--><?//=$download_link;?><!--" >Dowload Invite</a>-->
<!--</div>-->
<!---->
