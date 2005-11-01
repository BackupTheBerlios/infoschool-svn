function writeHTML(){
 for(var i=0;i<anz;i++){
  document.writeln('<span id="kasten'+i+'" class="versteckt"><center>'+get_rand(1)+'</center></span>');
 }
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

function run(){
 var x0 = get_x0() + 1;
 var y0 = get_y0();
 var w = get_width();
 var h = get_height();
 for(var i=0;i<anz;i++){
  y[i]+= 13;
  if((y[i]+13)>(h+y0)){
   x[i]+= 13;
   y[i] = y0;
  }
  if((x[i]+13)>(w+x0)) x[i] = x0;
  with(window.document.getElementById('kasten'+i).style){
   left = x[i]+'px';
   top = y[i]+'px';
  }
 }
 if(pause()) window.setTimeout('run()',maxspeed);
 else stop();
}

function start(){
 var x0 = get_x0() + 1;
 var y0 = get_y0();
 for(var i=0;i<anz;i++){
  x[i] = 13*get_rand(4) + x0;
  y[i] = 13*get_rand(32)+ y0;
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
 run();
}

var anz = 32;
x = new Array();
y = new Array();