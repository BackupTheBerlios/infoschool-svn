function writeHTML(){
 document.writeln('<span id="kasten" class="versteckt"></span>');
}

function stop(){
 with(window.document.getElementById('kasten').style){
  visibility = 'hidden';
  width = '0px';
  height = '0px';
  backgroundColor = '#000060';
  color = '#000060';
  top = '1px';
  left = '1px';
 }
 window.document.getElementById('main').style.visibility = 'visible';
 load();
}

function mod_pos(){
 var x0 = get_x0() + 1;
 var y0 = get_y0() + 1;
 var w = get_width();
 var h = get_height();
 var s = get_rand(1);
 if(s==0) s = -1;
 var d = s*13;
 if(get_rand(1)) x += d;
 else y += d;
 if(x<x0) x+= w;
 if((x+13)>(x0+w)) x-= w;
 if(y<y0) y+= h;
 if((y+13)>(y0+h)) y-= h;
}

function run(t){
 mod_pos();
 with(window.document.getElementById('kasten').style){
  left = x+'px';
  top = y+'px';
 }
 if(t>maxspeed)t--;
 if(pause()) window.setTimeout('run('+t+')',t);
 else stop();
}

function start(){
 x = get_x0() + 13*Math.floor(get_width()/13/2) + 1;
 y = get_y0() + 13*Math.floor(get_height()/13/2) + 1;
 with(window.document.getElementById('kasten').style){
  width = '12px';
  height = '12px';
  backgroundColor = '#800000';
  visibility = 'visible';
 }
 window.document.getElementById('main').style.visibility = 'hidden';
 run(1000);
}

var x,y = 0;