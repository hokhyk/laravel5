 
xrandr
vagrant@hokCom:~$ xrandr
Screen 0: minimum 320 x 200, current 3840 x 2160, maximum 8192 x 8192
VGA-1 disconnected (normal left inverted right x axis y axis)
DP-1 disconnected (normal left inverted right x axis y axis)
HDMI-1 disconnected (normal left inverted right x axis y axis)
   3840x2160_30  30.00  
DP-2 disconnected (normal left inverted right x axis y axis)
HDMI-2 connected primary 3840x2160+0+0 (normal left inverted right x axis y axis) 621mm x 341mm
   1920x1080     60.00    60.00    50.00    59.94  
   1920x1080i    60.00    50.00    59.94  
   1680x1050     59.88  
   1280x1024     75.02    60.02  
   1440x900      59.90  
   1280x960      60.00  
   1280x720      60.00    50.00    59.94  
   1024x768      75.03    70.07    60.00  
   832x624       74.55  
   800x600       72.19    75.00    60.32    56.25  
   720x576       50.00  
   720x480       60.00    59.94  
   640x480       75.00    72.81    66.67    60.00    59.94  
   720x400       70.08  
   3840x2160_30  30.00* 

xrandr -q
cvt 3840 2160 60
cvt 2560 1440 60
xrandr --newmode "2560x1440_60"  312.25  2560 2752 3024 3488  1440 1443 1448 1493 -hsync +vsync

xrandr --newmode "3840x2160_60"  712.34  3840 4152 4576 5312  2160 2161 2164 2235  -HSync +Vsync

xrandr --newmode "3840x2160_30"  339.57  3840 4080 4496 5152  2160 2161 2164 2197  -HSync +Vsync

xrandr --addmode DP-1 "3840x2160_60"
xrandr --addmode DP-1 "3840x2160_30"
xrandr --addmode DP-1 "2560x1440_60"
xrandr --output DP-1 --mode "3840x2160_30"
xrandr --output DP-1 --mode "3840x2160_60"


xrandr --addmode DP-2 "3840x2160_60"
xrandr --output DP-1 --mode "3840x2160_60"
xrandr --output DP-1 --mode "3840x2160_30"

xrandr --output DP-2 --mode "3840x2160_60"

xrandr --addmode HDMI-1 "3840x2160_30"
xrandr --addmode HDMI-2 "3840x2160_30"
xrandr --output HDMI-1 --mode "3840x2160_30"
xrandr --output HDMI-2 --mode "3840x2160_30"
