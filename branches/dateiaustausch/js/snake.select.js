function writeHTML(){
 for(var i=0;i<maxanz;i++){
  document.writeln('<span id="kasten'+i+'" class="versteckt"></span>');
 }
}

function mod_pos(i){
 x[i] = x[i+1];
 y[i] = y[i+1];
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
 anz=2;
 load();
}

function addKasten(){
 with(window.document.getElementById('kasten'+anz).style){
  left = x[anz]+'px';
  top = y[anz]+'px';
  width = '12px';
  height = '12px';
  backgroundColor = 'rgb('+get_rand(255)+','+get_rand(255)+','+get_rand(255)+')';
  visibility = 'visible';
 }
 anz++;
 x[anz] = x[anz-1];
 y[anz] = y[anz-1];
}

function run(t){
 var x0 = get_x0() +1;
 var y0 = get_y0() +1;
 var w = get_width();
 var h = get_height();
 if(x[0]==x[anz] && y[0] == y[anz]){
  if(anz<maxanz){
   var s = 1;
   if(get_rand(1)) s = -1;
   if(get_rand(1)) x[anz]+= 13*s;
   else y[anz]+= 13*s;
   addKasten();
  }
  x[0] = x0 + 13*(get_rand(w/13 -2));
  y[0] = y0 + 13*(get_rand(h/13 -2));
  with(window.document.getElementById('kasten0').style){
   left = x[0]+'px';
   top = y[0]+'px';
  }
 }
 else{
  var dx = 0;
  var dy = 0;
  if(x[anz]<x[0]) dx =  1;
  if(x[anz]>x[0]) dx = -1;
  if(y[anz]<y[0]) dy =  1;
  if(y[anz]>y[0]) dy = -1;
  if(x[anz]==x[0]){
   y[anz]+= 13*dy;
  }
  else{
   if(y[anz]==y[0]){
    x[anz]+= 13*dx;
   }
   else{
    if(get_rand(1)) x[anz] += 13*dx;
    else y[anz] += 13*dy;
   }
  }
  for(var i=1;i<anz;i++){
   mod_pos(i);
   with(window.document.getElementById('kasten'+i).style){
    left = x[i]+'px';
    top = y[i]+'px';
   }
  }
 }
 if(t>maxspeed)t--;
 if(pause()) window.setTimeout('run('+t+')',t);
 else stop();
}

function start(){
 var speed = 1000;
 x0 = get_x0() + 1;
 y0 = get_y0() + 1;
 x[0] = x0 + 13*(get_rand(get_width() /13 -2));
 y[0] = y0 + 13*(get_rand(get_height()/13 -2));
 x[1] = get_x0() + 1;
 y[1] = get_y0() + 13*Math.floor(get_height()/13/2)+1;
 x[anz] = x[1] + 13*(anz-1);
 y[anz] = y[1];
 for(var i=0;i<anz;i++){
  with(window.document.getElementById('kasten'+i).style){
   left = x[i]+'px';
   top = y[i]+'px';
   width = '12px';
   height = '12px';
   backgroundColor = 'rgb('+get_rand(255)+','+get_rand(255)+','+get_rand(255)+')';
   visibility = 'visible';
  }
 }
 window.document.getElementById('main').style.visibility = 'hidden';
 window.setTimeout('run('+speed+')',speed);
}

x = new Array();
y = new Array();
var maxanz = 64;
var anz = 2;
