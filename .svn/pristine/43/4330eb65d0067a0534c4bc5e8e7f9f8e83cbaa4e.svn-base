#/usr/bin/sh
old='</body>'
new='<div class="bs-header"><div class="container"><ul> <li><a class="download-btn" href="http://52.74.39.126/hj_app/app_article/detail/camera360.html">More</a></li></ul><div class="nav-logo"><a class="logo" href="http://52.74.39.126/hj_app/app_article/detail/camera360.html"><img width="48" height="48" src="http://52.74.39.126/hj_app/app_article/detail/source/camera360/icon.png"></a><a class="slogan" href="http://52.74.39.126/hj_app/app_article/detail/camera360.html">Camera360<span> Camera 360 degree </span></a></div></div></div></body>'
old2='</head>'
new2='<link href="http://52.74.39.126/hj_app/app_article/css/packed__init_-b2a203a6c77ec2147d085b10c5821d1a.css" rel="stylesheet" type="text/css"> <link href="http://52.74.39.126/hj_app/app_article/css/packed_post-3dbfa8e87c6c8e45546afc0bbcc2066b.css" rel="stylesheet" type="text/css"></head>'
#mitmproxy --host -s "mitmproxy/examples/modify_response_body.py \"$old2\" \"$new2\"" -s "mitmproxy/examples/modify_response_body.py \"$old\" \"$new\""
mitmproxy -T --host -s "mitm_scripts/modify_response_body.py \"$old2\" \"$new2\"" -s "mitm_scripts/modify_response_body.py \"$old\" \"$new\""

