import mysql.connector
import os
import json

db = mysql.connector.connect(
    host="35.232.166.198",
    user="root",
    password="pr0toc@\\76h;;",
    database="wynnguessr"
)
cursor = db.cursor()
root_dir = "/home/ammel/.minecraft/config/panoramica/WITH COORDS/"
dirs = [x[0] for x in os.walk(root_dir)]
dirs.pop(0)
for panorama_dir in dirs:
    dir_name = os.path.basename(panorama_dir)
    cubemap_file = open(dir_name + ".json", "w")
    cubemap_data = {
        "type": "cubemap",
        "cubeMap": [
            "../imgs/panoramas/" + dir_name +"/panorama_0.png",
            "../imgs/panoramas/" + dir_name +"/panorama_1.png",
            "../imgs/panoramas/" + dir_name +"/panorama_2.png",
            "../imgs/panoramas/" + dir_name +"/panorama_3.png",
            "../imgs/panoramas/" + dir_name +"/panorama_4.png",
            "../imgs/panoramas/" + dir_name +"/panorama_5.png"
        ],
        "autoLoad": True,
        "showControls": False
    }
    json.dump(cubemap_data,cubemap_file,ensure_ascii=False, indent=4)
    cubemap_file.close()
    

    # coords_file = open(panorama_dir + "/coords.xyz")
    # coords = coords_file.readlines()
    # x = coords[0].strip("X:").strip("\n")
    # z = coords[2].strip("Z:").strip("\n")
    # lng = int(x)+2391.5
    # lat = abs(int(z))-125.5
    # sql = ("INSERT INTO panoramas (`cubemap-filename`, lat,lng) VALUES (%s,%s,%s)")
    # val = (dir_name,lat,lng)
    # cursor.execute(sql,val)
    # db.commit()

