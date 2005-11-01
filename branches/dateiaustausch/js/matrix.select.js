function writeHTML(){
 var n = new Array();
 var i = 0;
 for(var v=33;v<38;v++) n[i++] = v;
 for(v=40;v<64;v++) n[i++] = v;
 for(v=91;v<96;v++) n[i++] = v;
 for(v=123;v<126;v++) n[i++] = v;
 for(v=12449;v<12535;v++) n[i++] = v;
 for(i=0;i<anz;i++){
  for(var j=0;j<l;j++){
   document.writeln('<span id="kasten'+i+'a'+j+'" class="versteckt"><center>&#'+(n[get_rand(n.length-1)])+';</center></span>');
  }
 }
}

function stop(){
 for(var i=0;i<anz;i++){
  for(var j=0;j<l;j++){
   with(window.document.getElementById('kasten'+i+'a'+j).style){
    visibility = 'hidden';
    width = '0px';
    height = '0px';
    backgroundColor = '#000060';
    color = '#000060';
    top = '1px';
    left = '1px';
   }
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
  for(var j=(l-1);j>0;j--){
   with(window.document.getElementById('kasten'+i+'a'+j).style){
    left = window.document.getElementById('kasten'+i+'a'+(j-1)).style.left;
    top = window.document.getElementById('kasten'+i+'a'+(j-1)).style.top;
   }
  }
  with(window.document.getElementById('kasten'+i+'a0').style){
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
 var fi = get_rand(4);
 for(var i=0;i<anz;i++){
  x[i] = x0 + 13*i;
  y[i] = y0 + 13*get_rand(get_height()/13);
  for(var j=0;j<l;j++){
   with(window.document.getElementById('kasten'+i+'a'+j).style){
    left = x[i]+'px';
    top = y[i] - 13*j+'px';
    width = '12px';
    height = '12px';
    fontSize = '12px';
    backgroundColor = 'transparent';
    switch(fi){
     case 0: color = '#00ff00'; break;
     case 1: color = 'rgb('+(128+(l-j)*127/l)+',255,'+(128+(l-j)*127/l)+')'; break;
     case 2: color = 'rgb('+((l-j)*255/l)+',255,00)'; break;
     case 3: color = 'rgb(255,'+((l-j)*255/l)+',255)'; break;
     default: color = '#ffffff'; break;
    }
    visibility = 'visible';
   }
  }
 }
 window.document.getElementById('main').style.visibility = 'hidden';
 run();
}

var l = 16;
var anz = 4;
if(navigator.appName=='Netscape'){
 l = 10;
 anz = 1;
}
if(get_width()/13 < anz) anz = get_width()/13;
var x = new Array();
var y = new Array();
