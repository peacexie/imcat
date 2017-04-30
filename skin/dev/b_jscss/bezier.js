
// ============================================ 

function DrawPoint(x,y,_context){
  var cans=_context;
  var cxt=_context;
  cxt.beginPath();
  cxt.arc(x,y,1,0,Math.PI*2,true);
  cxt.closePath();
  cxt.fill();
  return;
  //cans.moveTo(x,y);//第一个起点
  cans.lineTo(x,y);//第二个点
  //cans.lineTo(220,60);//第三个点（以第二个点为起点）
  cans.lineWidth=1;
  cans.strokeStyle = 'red';
  cans.stroke();
}

// ============================================ 

//--线性bezier 曲线求值，根据t值来求值，t取值为[0,1]，可以认为是0就在起点，1就在终点，是过程的百分比位置。
var LinearBezier=function(point_start,point_end){
  var p_start={x:0,y:0};
  var p_end={x:0,y:0};
  p_start=point_start;
  p_end=point_end;
  //--计算t为某个值得时候的点。
  //--计算公式。
  /**
   *
   * [1 t]| 1   0| |P0|
   *      |-1   1| |P1|
   *
   * */
  this.getPoint=function(t){
    var _m_1_1=1-t;
    var _m_1_2= t;  //1*0+1*1+_t*0+_t*1;
    var _t_1_1= (1-t)*p_start.x+t*p_end.x; //0+(_t+1)*p_start.x+(_t+1)*p_end.x;
    var _t_1_2=(1-t)*p_start.y+t*p_end.y;   //0+(_t+1)*p_start.y+(_t+1)*p_end.y;
    var _res_point={
      x:_t_1_1
      ,y:_t_1_2
    };
    return _res_point;
  };
};

//--二次方bezier
var SquareBezier=function(start_point,crt_point,end_point){
  var p_start={x:0,y:0};
  var p_end={x:0,y:0};
  p_start=start_point;
  p_end=end_point;
  var p_crt1=crt_point;
  /**
   * 计算公式：
   *            | 1  0  0|  |P0|
   * [1 t t*t ] |-2  2  0|  |P1|
   *            |1  -2  1|  |P2|
   * **/
  this.getPoint=function(t){
    var _matrix1=[1,t,t*t];
    var _matrix2=[
      [1,0,0]
      ,[-2,2,0]
      ,[1,-2,1]
    ];
    var _matrix3=[
          [p_start.x,p_start.y]
          ,[p_crt1.x,p_crt1.y]
          ,[p_end.x,p_end.y]
    ];
    var _matrix_tmp=[
      _matrix1[0]*_matrix2[0][0]+_matrix1[1]*_matrix2[1][0]+_matrix1[2]*_matrix2[2][0]
      ,_matrix1[0]*_matrix2[0][1]+_matrix1[1]*_matrix2[1][1]+_matrix1[2]*_matrix2[2][1]
      ,_matrix1[0]*_matrix2[0][2]+_matrix1[1]*_matrix2[1][2]+_matrix1[2]*_matrix2[2][2]
    ];
    var _matrix_final=[
            _matrix_tmp[0]*_matrix3[0][0]+_matrix_tmp[1]*_matrix3[1][0]+_matrix_tmp[2]*_matrix3[2][0]
            ,_matrix_tmp[0]*_matrix3[0][1]+_matrix_tmp[1]*_matrix3[1][1]+_matrix_tmp[2]*_matrix3[2][1]
    ];
    var _res_point={
      x:_matrix_final[0]
      ,y:_matrix_final[1]
    };
    return _res_point;
  };

};//

var CubeBezier=function(point_start,point1,point2,point_end){
  var p_start={x:0,y:0};
  var p_end={x:0,y:0};
  p_start=point_start;
  p_end=point_end;
  var p_crt1=point1;
  var p_crt2=point2;
  /**
   * 计算公式：
   *                  | 1  0  0   0|  |P0|
   * [1 t t*t  t*t*t] |-3  3  0   0|  |P1|
   *                  |3  -6  3   0|  |P2|
   *                  |-1  3  -3  1|  |p3|
   *
   * **/
  this.getPoint=function(t){
    var _matrix1=[1,t,t*t,t*t*t];
    var _matrix2=[
      [1,0,0,0]
      ,[-3,3,0,0]
      ,[3,-6,3,0]
      ,[-1,3,-3,1]
    ];
    var _matrix3=[
      [p_start.x,p_start.y]
      ,[p_crt1.x,p_crt1.y]
      ,[p_crt2.x,p_crt2.y]
      ,[p_end.x,p_end.y]
    ];
    var _matrix_tmp=[
      _matrix1[0]*_matrix2[0][0]+_matrix1[1]*_matrix2[1][0]+_matrix1[2]*_matrix2[2][0]+_matrix1[3]*_matrix2[3][0]
      ,_matrix1[0]*_matrix2[0][1]+_matrix1[1]*_matrix2[1][1]+_matrix1[2]*_matrix2[2][1]+_matrix1[3]*_matrix2[3][1]
      ,_matrix1[0]*_matrix2[0][2]+_matrix1[1]*_matrix2[1][2]+_matrix1[2]*_matrix2[2][2]+_matrix1[3]*_matrix2[3][2]
      ,_matrix1[0]*_matrix2[0][3]+_matrix1[1]*_matrix2[1][3]+_matrix1[2]*_matrix2[2][3]+_matrix1[3]*_matrix2[3][3]
    ];
    var _matrix_final=[
      _matrix_tmp[0]*_matrix3[0][0]+_matrix_tmp[1]*_matrix3[1][0]+_matrix_tmp[2]*_matrix3[2][0]+_matrix_tmp[3]*_matrix3[3][0]
      ,_matrix_tmp[0]*_matrix3[0][1]+_matrix_tmp[1]*_matrix3[1][1]+_matrix_tmp[2]*_matrix3[2][1]+_matrix_tmp[3]*_matrix3[3][1]
    ];
    var _res_point={
      x:_matrix_final[0]
      ,y:_matrix_final[1]
    };
    return _res_point;
  };
};//

// ============================================ 

//--演示程序。线性贝塞尔 ==================================

var linear_start_x=$("#linear_start_x");
var linear_start_y=$("#linear_start_y");
var linear_end_x=$("#linear_end_x");
var linear_end_y=$("#linear_end_y");
var canvas_linear=document.getElementById("canvas_linear");
var context_linear=canvas_linear.getContext("2d");
var btn_linear=$("#btn_linear");
var linear_showing=false;
var linear_debug=$("#linear_debug");

btn_linear.click(function(){
  if(linear_showing==true){
    linear_debug.text("程序正在进行演示，请稍后再进行操作。");
    return;
  }
  linear_showing=true;
  context_linear.clearRect(0,0,canvas_linear.width,canvas_linear.height);
  //--绘制两个点。
  var _start_point={
    x:parseInt(linear_start_x.val())
    ,y:parseInt(linear_start_y.val())
  };
  var _end_point={
    x:linear_end_x.val()
    ,y:linear_end_y.val()
  };
  DrawPoint(_start_point.x,_start_point.y,context_linear);
  DrawPoint(_end_point.x,_end_point.y,context_linear);

  var _bezier=new LinearBezier(_start_point,_end_point);
  function linearDraw(t){
    var _t_point=_bezier.getPoint(t);
    console.log(_t_point);
    DrawPoint(_t_point.x,_t_point.y,context_linear);
  }

  var _now_t=0.01;
  var _interval1=setInterval(function(){
    if(_now_t>=1){
      clearInterval(_interval1);
      console.log("演示结束");
      linear_showing=false;
      return;
    }
    linearDraw(_now_t);
    _now_t=_now_t+0.01;
  },100);
});


//--演示程序。二维贝塞尔 ==================================

var sqrt_start_x=$("#sqrt_start_x");
var sqrt_start_y=$("#sqrt_start_y");
var sqrt_end_x=$("#sqrt_end_x");
var sqrt_end_y=$("#sqrt_end_y");
var sqrt_crt1_x=$("#sqrt_crt1_x");
var sqrt_crt1_y=$("#sqrt_crt1_y");
var canvas_sqrt=document.getElementById("canvas_sqrt");
var context_sqrt=canvas_sqrt.getContext("2d");
var btn_sqrt=$("#btn_sqrt");
var sqrt_showing=false;
var sqrt_debug=$("#sqrt_debug");

btn_sqrt.click(function(){
  if(sqrt_showing==true){
    sqrt_debug.text("程序正在进行演示，请稍后再进行操作。");
    return;
  }
  sqrt_showing=true;
  context_sqrt.clearRect(0,0,canvas_sqrt.width,canvas_sqrt.height);
  //--绘制两个点。
  var _start_point={
    x:parseInt(sqrt_start_x.val())
    ,y:parseInt(sqrt_start_y.val())
  };
  var _end_point={
    x:parseInt(sqrt_end_x.val())
    ,y:parseInt(sqrt_end_y.val())

  };
  var _crt_point1={
    x:parseInt(sqrt_crt1_x.val())
    ,y:parseInt(sqrt_crt1_y.val())
  };
  DrawPoint(_start_point.x,_start_point.y,context_sqrt);
  DrawPoint(_end_point.x,_end_point.y,context_sqrt);
  DrawPoint(_crt_point1.x,_crt_point1.y,context_sqrt);

  var _bezier=new SquareBezier(_start_point,_crt_point1,_end_point);
  function sqrtDraw(t){
    var _t_point=_bezier.getPoint(t);
    console.log(_t_point);
    DrawPoint(_t_point.x,_t_point.y,context_sqrt);
  }

  var _now_t=0.01;
  var _interval1=setInterval(function(){
    if(_now_t>=1){
      clearInterval(_interval1);
      console.log("演示结束");
      sqrt_showing=false;
      return;
    }
    sqrtDraw(_now_t);
    _now_t=_now_t+0.01;
  },100);
});


//--演示程序。三次贝塞尔 ==================================

var cube_start_x=$("#cube_start_x");
var cube_start_y=$("#cube_start_y");
var cube_end_x=$("#cube_end_x");
var cube_end_y=$("#cube_end_y");
var cube_crt1_x=$("#cube_crt1_x");
var cube_crt1_y=$("#cube_crt1_y");
var cube_crt2_x=$("#cube_crt2_x");
var cube_crt2_y=$("#cube_crt2_y");
var canvas_cube=document.getElementById("canvas_cube");
var context_cube=canvas_cube.getContext("2d");
var btn_cube=$("#btn_cube");
var cube_showing=false;
var cube_debug=$("#cube_debug");

btn_cube.click(function(){
  if(cube_showing==true){
    cube_debug.text("程序正在进行演示，请稍后再进行操作。");
    return;
  }
  cube_showing=true;
  context_cube.clearRect(0,0,canvas_cube.width,canvas_cube.height);
  //--绘制两个点。
  var _start_point={
    x:parseInt(cube_start_x.val())
    ,y:parseInt(cube_start_y.val())
  };
  var _end_point={
    x:parseInt(cube_end_x.val())
    ,y:parseInt(cube_end_y.val())

  };
  var _crt_point1={
    x:parseInt(cube_crt1_x.val())
    ,y:parseInt(cube_crt1_y.val())
  };
  var _crt_point2={
    x:parseInt(cube_crt2_x.val())
    ,y:parseInt(cube_crt2_y.val())
  };
  DrawPoint(_start_point.x,_start_point.y,context_cube);
  DrawPoint(_end_point.x,_end_point.y,context_cube);
  DrawPoint(_crt_point1.x,_crt_point1.y,context_cube);
  DrawPoint(_crt_point2.x,_crt_point2.y,context_cube);

  var _bezier=new CubeBezier(_start_point,_crt_point1,_crt_point2,_end_point);
  function cubeDraw(t){
    var _t_point=_bezier.getPoint(t);
    console.log(_t_point);
    DrawPoint(_t_point.x,_t_point.y,context_cube);
  }

  var _now_t=0.01;
  var _interval1=setInterval(function(){
    if(_now_t>=1){
      clearInterval(_interval1);
      console.log("演示结束");
      cube_showing=false;
      return;
    }
    cubeDraw(_now_t);
    _now_t=_now_t+0.01;
  },100);
});
