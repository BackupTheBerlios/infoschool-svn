        
function inc_minute(hour,minute) {
 minute++;
 if (minute > 59) {
  minute = "0";
  hour++;
 }
 if (hour > 23) {
  hour = "0";
 }
 if(minute < 10) minute = "0" + minute;
 if(hour < 10) hour = "0" + hour; 
  
 window.document.getElementById("current_hour").firstChild.nodeValue = hour;
 window.document.getElementById("current_minute").firstChild.nodeValue = minute;
 window.setTimeout("inc_minute("+hour+","+minute+")",60*1000);
}
  
