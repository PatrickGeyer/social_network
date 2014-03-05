//Useful functions and classes when using the canvas

function toRadians(degrees) {
    return degrees*(Math.PI*180);
}
function toDegrees(radians) {
    return radians/(Math.PI*180);
}

function Circle(x, y, rad, cont){
    var context = cont;
    var xPos = x;
    var yPos = y;
    var radius = rad;
    var fillColour = "black";
    var lineColour = "black";
    var lineWidth = 1;
    this.setColour = function(colour) {
        fillColour = colour;
    };
    this.setLineColour = function(colour) {
        lineColour = colour;
    };
    this.setLineWidth = function(lWidth) {
        lineWidth  = lWidth;
    };
    this.setPosition = function(x, y) {
        xPos = x;
        yPos = y;
    };
    this.setRadius = function(rad) {
        radius = rad;
    };
    this.getRadius = function() {
        return radius;
    };
    this.getXPosition = function() {
        return xPos;
    };
    this.geYPosition = function() {
        return yPos;
    };
    this.moveCircle = function(moveX, moveY) {
        remove();
        xPos += moveX;
        yPos += moveY;
        draw();
    };
    function remove() {
        context.fillStyle("white");
        context.strokeStyle("white");
        context.lineWidth(lineWidth);
        context.beginPath();
        context.arc(xPos, yPos, radius, 0, Math.PI*2, false);
        context.closePath();
        context.fill();
    }
    function draw() {
        context.fillStyle = fillColour;
        context.strokeStyle = lineColour;
        context.lineWidth = lineWidth;
        context.beginPath();
        context.arc(xPos, yPos, radius, 0, Math.PI*2, false);
        context.closePath();
        context.fill();
    }
}

function Rectangle(x, y, wid, hei, cont, colour) {
    var context = cont;
    var xPos = x;
    var yPos = y;
    var width = wid;
    var height = hei;
    var fillColour = colour;
    var lineColour = "black";
    var lineWidth = 1;
    this.setColour = function(colour) {
        fillColour = colour;
    };
    this.setLineColour = function(colour) {
        lineColour = colour;
    };
    this.setLineWidth = function(lWidth) {
        lineWidth  = lWidth;
    };
    this.setPosition = function(x, y) {
        xPos = x;
        yPos = y;
    };
    this.changeSize = function(w, h) {
        width = w;
        height = h;
    };
    this.getWidth = function() {
        return width;
    };
    this.getHeight = function() {
        return height;
    };
    this.getXPosition = function() {
        return xPos;
    };
    this.geYPosition = function() {
        return yPos;
    };
    this.add = function() {
        draw();
    };
    this.moveRect = function(moveX, moveY) {
        remove();
        xPos += moveX;
        yPos += moveY;
        draw();
    };
    this.remove = function() {
        remove();
    };
    function draw() {
        context.fillStyle = fillColour;
        context.strokeStyle = lineColour;
        context.lineWidth = lineWidth;
        context.fillRect(xPos, yPos, width, height);
    }
    function remove() {
        context.clearRect(xPos, yPos, width, height);
    }
}