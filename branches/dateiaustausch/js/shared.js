/*
 Funktionen zur Zeitsteuerung und Umgebeung der JavaScript-Dateien
 Aufgabe der Skripte: Vermeidung von Langeweile
*/

// gibt die Position des linken sichtbaren Randes zurück
function get_x0(){
 var x0 = document.body.scrollLeft;
 if(screen.pageXOffset) x0 = screen.pageXOffset;
 var k = Math.floor(x0/13);
 return 13*k;
}

// gibt die Position des oberen sichtbaren Randes zurück
function get_y0(){
 var y0 = document.body.scrollTop;
 if(screen.pageYOffset) y0 = screen.pageYOffset;
 var k = Math.floor(y0/13);
 return 13*k;
}

// gibt die Breite des Fensters zurück
function get_width(){
 var w = screen.availWidth -10;
 if(window.innerWidth) w = window.innerWidth;
 var k = Math.floor(w/13);
 return 13*k;
}

// gibt die Höhe des Fensters zurück
function get_height(){
 var h = screen.availHeight -130;
 if(window.innerHeight) h = window.innerHeight;
 var k = Math.floor(h/13);
 return 13*k;
}

// gibt den aktuelle Zeit in Millisekunden zurück
function get_time(){
 var d = new Date();
 return d.getTime();
}

// setzt die Zeit der letzten Aktivität (t0) auf die aktuelle
function reset_time(){
 t0 = get_time();
}

// gibt eine Zufallszahl zwischen 0 und m zurück
function get_rand(m){
 return Math.round(m*Math.random());
}

// ist wahr, wenn seit der letzten Aktivität eine bestimmte Zeit (dif) vergangen ist
function pause(){
 if(pause_time>0 && (get_time()-t0)>pause_time) return true;
 else return false;
}

// guckt einmal pro Sekunde nach, ob der User noch aktiv ist
function load(){
 if(pause()){
  start();
 }
 else{
  if(pause_time>0)
   window.setTimeout('load()',Math.round(pause_time/10));
 }
}

// Zeitpunkt der letzten Aktivität (Mausbewegung oder Tastendruck)
var t0 = get_time();
document.onkeydown = reset_time;
document.onmousemove = reset_time;