//generic panorama class used in both game.js and replay.js, self explanatory
class Panorama{
    constructor(path){
        //documentation for more information (pannellum.org)
        this.path = "pannellum.htm#config=../../../configs/" + path;
    }

    getPath(){
        return this.path;
    }
}
