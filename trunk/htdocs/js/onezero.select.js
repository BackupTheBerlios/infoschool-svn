function writeHTML(){
 for(var i=0;i<anz;i++){
  document.writeln('<span id="kasten'+i+'" class="versteckt"><center>'+get_rand(1)+'</center></span>');
 }
}

function mod_pos(i){
 var x0 = get_x0() +1;
 var y0 = get_y0();
 var w = get_width();
 var h = get_height();
 var s = get_rand(1);
 if(s==0) s = -1;
 var d = s*13;
 if(get_rand(1)) x[i] += d;
 else y[i] += d;
 if(x[i]<x0) x[i]+= w;
 if((x[i]+13)>(x0+w)) x[i]-= w;
 if(y[i]<y0) y[i]+= h;
 if((y[i]+13)>(y0+h)) y[i]-= h;
}

function stop(){
 for(var i=0;i<anz;i++){
  with(window.document.getElementById('kasten'+i).style){
   visibility = 'hidden';
   width = '0px';
   height = '0px';
   backgroundColor = '#000060';
   color = '#000060';
   top = '1px';
   left = '1px';
  }
 }
 window.document.getElementById('main').style.visibility = 'visible';
 load();
}

function run(t){
 for(var i=0;i<anz;i++){
  mod_pos(i);
  with(window.document.getElementById('kasten'+i).style){
   left = x[i]+'px';
   top = y[i]+'px';
  }
 }
 if(t>maxspeed)t--;
 if(pause()) window.setTimeout('run('+t+')',t);
 else stop();
}

function start(){
 var speed = 1000;
 for(var i=0;i<anz;i++){
  x[i] = get_x0() + 13*Math.floor(get_width()/13/2) +1;
  y[i] = get_y0() + 13*Math.floor(get_height()/13/2);
  with(window.document.getElementById('kasten'+i).style){
   left = x[i]+'px';
   top = y[i]+'px';
   width = '12px';
   height = '12px';
   fontSize = '12px';
   backgroundColor = 'transparent';
   color = '#ffffff';
   visibility = 'visible';
  }
 }
 window.document.getElementById('main').style.visibility = 'hidden';
 window.setTimeout('run('+speed+')',speed);
}

x = new Array();
y = new Array();
var anz = 32;