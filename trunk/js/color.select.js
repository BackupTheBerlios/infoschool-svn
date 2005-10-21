function writeHTML(){
 for(var i=0;i<anz;i++){
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
 load();
}

function run(t){
 var x0 = get_x0() +1;
 var y0 = get_y0() +1;
 var w = get_width();
 var h = get_height();
 if(get_rand(anz)==0) xs = -xs;
 if(get_rand(anz)==0) ys = -ys;
 if(get_rand(1)) x[anz] += 13*xs;
 else y[anz] += 13*ys;
 if(x[anz]<x0) x[anz]+= w;
 if((x[anz]+13)>(x0+w)) x[anz]-= w;
 if(y[anz]<y0) y[anz]+= h;
 if((y[anz]+13)>(y0+h)) y[anz]-= h;
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
 x0 = get_x0() + 1;
 y0 = get_y0() + 13*Math.floor(get_height()/13/2)+1;
 x[anz] = x0 + 13*(anz-1);
 y[anz] = y0;
 for(var i=0;i<anz;i++){
  x[i] = x0 + 13*i;
  y[i] = y0;
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
var anz = 32;
var xs = 1;
var ys = 1;