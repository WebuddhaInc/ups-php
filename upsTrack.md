# Introduction #

To use upsTrack you must require the function upsTrack.php

```
require("upsTrack.php");
```


# Details #

Example of getting the entire xml response in an array

```
$myTrack = new upsTrack('accessnumber','username','password');
$myArray = $myRate->getTrack('trackingNumber');
```

Example of getting the scheduled delivery date

```
echo $myTrack->getDeliveryDate();
```

Example of getting the pickup date

```
echo $myTrack->getPickupDate();
```