<?php 
/**
 * Explain:CeshiApiController.php
 * 网站公用api
 */
class ApiController extends Controller
{
    /** 状态码 */
    const A1001 = "未传入序列号";
    const A1002 = "序列号位数错误";
    const A1003 = "无此序列号";
    const A1004 = "序列号已被封";
    const A1005 = "密钥错误";
    const A1006 = "IMEI码位数错误";
    const A1007 = "序列号不是数字";
    const A1008 = "IMEI码不是数字";
    const A1009 = "无效访问";
    const A1010= "此业务在手机上已失效";
    const A1011= "序列号范围错误";
    const K1001 = "接口参数不能为空";
    const K1002 = "用户未开启任何业务";
    const K1003 = "对应业务已关闭";
    const K1004 = "无对应散量业务";
  
    const C1001 = "上报类型错误";
    const C1002 = "上报MAC地址错误";
    const C1003 = "上报IMEI码位数错误";
    const C1004 = "上报IMEI码不是数字";
    const C1005 = "上报UID无效";
    const C1006 = "上报业务名称无效";
    const C1007 = "上报手机IMEI码无效或无对应用户记录";
    const C1008 ="上报分组数据为空";
    const C1009 ="业务包md5值错误";
    const KEY = "6E4632F5CA29833F76E09158252472EE";
    private static $secret_key = array(
        "7"=>"b9c80a22a043116b25f891fbc5c1bb6f",
        "8"=>"b9c80a22a043112b25f891fjc5c1bb6f",
        "9"=>"bfcu0a22a94311cb25ff91fbc5c1bb6f",
        "10"=>"bfc80k22a0k31125f891fkbc5c1bb6f",
        "11"=>"bfc80a22a074311cb625f891fbc5b6f",
        "12"=>"bfc80a22a04d1cb25jd91fbc5c1bb6f",
        "13"=>"bfc80a22a04d1cb25fsoo1fbc5c3b6f",
        "14"=>"bfc80a22a04d1cb25fsdfbc5pc1bb6f",
        "15"=>"bfc80a22a04d1cb25fs91ffgdc1bb6f",
        "16"=>"bfc80a22a04d1cb25fs91fbc5c1bb6f",
        "17"=>"bfc80a22a04d1cb25fs91sdc5c1bb6f",
        "18"=>"bfc80a22a04d1cb25fs91fbc4c1bb6f",
        "19"=>"bfc80a22a04d1cb25fsfddfsdc1bb6f",
        "20"=>"bfcda22a04d1csfs91e3sfbc5c1bb6f"
    );

    public function actionIndex()
    {
        throw new CHttpException(404, ApiController::A1009);
    }
    // 获取数据
    public function actionSpoa2(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        header('Access-Control-Allow-Methods: POST,GET,HEAD,OPTIONS,PUT,DELETE,TRACE,CONNECT');
            $sql="select * from `app_node`";
            $result=yii::app()->db->createCommand($sql)->queryAll();
            $result=!empty($result) ? $result : array();
            $array=array(
                'jobLists'=>$result
            );
        echo json_encode($array);
    }
    // 新增数据
    public function actionCreate(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        header('Access-Control-Allow-Methods: POST,GET,HEAD,OPTIONS,PUT,DELETE,TRACE,CONNECT');
        $a = json_decode(file_get_contents("php://input"),true);
        $name= isset($a['name']) ? $a['name'] : 'error';
        $code= isset($a['code']) ? $a['code'] : 'error';

        $sql="INSERT INTO  `app_node` (`name`,`code`) values ('{$name}','{$code}')";
        $result=yii::app()->db->createCommand($sql)->query();
        if($result) {
            echo json_encode(array('success'=>true));
        }else{
            echo json_encode(array('success'=>false));
        }
        
    }
    // 删除数据
    public function actionDelete() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
        header('Access-Control-Allow-Methods: POST,GET,HEAD,OPTIONS,PUT,DELETE,TRACE,CONNECT');
        $id=$_GET['id'];
        $sql='delete from `app_node` where id='.$id;
        $result = yii::app()->db->createCommand($sql)->query();
        if($result){
            echo json_encode(array('success'=>true));
        }else{
            echo json_encode(array('success'=>false));
        }
    }


    /**
     *  ROM：J1-根据统计软件序列号+签名md5，获取用户正在做的所有业务列表
     *  https://www.sutuiapp.com/api/GetApplist?appNo=100001&sign=52d16d4a9117342cedd35592f3b0baee
     */
    public function actionGetApplist()
    {
        $j="J1";
        $appNo=Yii::app()->request->getParam('appNo');//序列号--规则6位，起始100001-999999
        $sign=Yii::app()->request->getParam('sign');
        $url="";
        //$url="appNo:".$appNo."-sign:".$sign;
        $key=self::KEY;
        //$cusign = md5($key.$appNo.date('Y-m-d'));//20170925时间经常获取不到，去掉
        $data = array(
            'success' => false,
            'msg' => array('apps'=>array())
        );

        //验证合法
        if ($appNo=="" || $sign=="") {
            $data['msg'] = "K1001";
            Common::printJson_tj($data,$url,$j);
        }
        if (is_numeric($appNo)==false) {
            $data['msg'] = "A1007";
            Common::printJson_tj($data,$url,$j);
        }
        if (strlen($appNo)!=6) {
            $data['msg'] = "A1002";
            Common::printJson_tj($data,$url,$j);
        }
        if ($appNo>999999 || $appNo<100000) {
            $data['msg'] = "A1011";
            Common::printJson_tj($data,$url,$j);
        }
        $model = RomSoftpak::model()->find('serial_number=:serial_number',array(':serial_number'=>$appNo));
        if (is_null($model)) {
            $data['msg'] = "A1003";
            Common::printJson_tj($data,$url,$j);
        }
/*        if ($sign!=$cusign) {
            $data['msg'] = "A1005";
            Common::printJson_tj($data,$url,$j);
        }*/ //20170925时间经常获取不到，去掉
        if ($model->closed==1) {
            $data['msg'] = "A1004";
            Common::printJson_tj($data,$url,$j);
        }
        //验证通过，回传数据给接口--{"success":xxxx,"msg":{"apps":[{"name":"xxxx","pakname":"xxxx","key":"xxxx"}]}}
        $uid=$model->uid;
        $member=Member::model()->getById($uid);
        //分组id
        $groupid=$member["agent"];

        $restypes="";
        //业务列表 $useresource,关闭业务不统计，不计费--此处返回所有业务，关闭、封号等在J2处理
        $useresource=MemberResource::model()->findAll('uid=:uid',array('uid'=>$uid));
        if(!empty($useresource))
        {
            foreach($useresource as $ur=>$val)
            {
                $appinfo=ProductList::model()->findAll('type=:type and status=:status and agent=:agent order by id asc limit 20',array(':type'=>$val['type'],':agent'=>$groupid,':status'=>1));
                //下架业务
                if(empty($appinfo))
                {
                    $restypes[$ur]['name']=$val['type'];
                    $restypes[$ur]['pakname']="";
                    $restypes[$ur]['key']="";
                }
                else
                {
                    $restypes[$ur]['name']=$val['type'];//业务名称
                    //业务有多个包，判断此md5和包名与手机内业务md5和包名相同否，相同则继续接口
                    if(count($appinfo)>=3)
                    {
                        //兼容老版本key，pakname
                        $restypes[$ur]['key']=$appinfo[0]['sign'];
                        $restypes[$ur]['pakname']=$appinfo[0]['pakname'];
                        //兼容老版本key2，pakname2
                        $restypes[$ur]['key2']=$appinfo[1]['sign'];
                        $restypes[$ur]['pakname2']=$appinfo[1]['pakname'];

                        //第三个包及以上处理--新版2016-08-23
                        foreach($appinfo as $ar=>$av)
                        {
                            if($ar>1)
                            {
                                $restypes[$ur]["ninfo"][]['key']=$av['sign'];
                                $restypes[$ur]["ninfo"][]['pakname']=$av['pakname'];
                            }

                        }

                    }
                    else
                    {
                        //兼容老版本，key和key2
                        $restypes[$ur]['key']=$appinfo[0]['sign'];
                        $restypes[$ur]['pakname']=$appinfo[0]['pakname'];

                        //业务有第二个包，如果有值--手机端再次判断此md5与手机内业务md5相同否，key/key2如有相同则在J3中上传此值
                        if(count($appinfo)==2)
                        {
                            $restypes[$ur]['key2']=$appinfo[1]['sign'];
                            $restypes[$ur]['pakname2']=$appinfo[1]['pakname'];
                        }
                    }


                }


            }
            //返回json数据
            if(!empty($restypes))
            {
                $data['success'] = true;
                $data['msg']['apps'] = $restypes;
                Common::printJson_tj($data,$url,$j);
            }
            else
            {
                $data['msg'] = "K1004";
                Common::printJson_tj($data,$url,$j);
            }

        }
        else
        {
            //返回json数据
            $data['msg'] = "K1002";
            Common::printJson_tj($data,$url,$j);
        }

        exit;
    }

    /**
     *  ROM：J2-客户端根据J1返回的数据和本机业务app做判断，客户端接口返回需要监视的业务app名称列表
     *  https://www.sutuiapp.com/api/GetUserApps?appNo=100001&tjcode=125&phcode=123456128946456&simcode=34werwre&sign=0c6824b6978c2fea3a80432d0a8ee683&model=fsdfs&applist=meituan,jrtt
     */
    public function actionGetUserApps()
    {
        $j="J2";
        $appNo=Yii::app()->request->getParam('appNo');
        $sign=trim(Yii::app()->request->getParam('sign'));
        $from=Yii::app()->request->getParam('from');
        $brand=Yii::app()->request->getParam('brand','');
        $sys=Yii::app()->request->getParam('sys','');
        $count=Yii::app()->request->getParam('count');
        if(!isset($from)) $from=0;
        if(!isset($count)) $count=3;//原rom包用户，老统计软件兼容
        $phcode=Yii::app()->request->getParam('phcode');//手机imei码，如获取不到则报空，空=此手机不能做业务
        $simcode=Yii::app()->request->getParam('simcode');//sim卡序列号
        $tjcode=Yii::app()->request->getParam('tjcode');//tongjiapp序列号
        $applist=Yii::app()->request->getParam('applist');//返回需要监视的业务app名称列表
        $model=Yii::app()->request->getParam('model');//手机型号
        $times=trim(Yii::app()->request->getParam('times'));//加密时间戳
        $url="";
        //$url="appNo:".$appNo."-sign:".$sign."-phcode:".$phcode."-simcode:".$simcode."-applist:".$applist."-model:".$model."-tjcode:".$tjcode;

        //$tjcode>=7开始使用新的加密方式--20160902
        if($tjcode>=7 && !empty($times))
        {
            if(!array_key_exists($tjcode,self::$secret_key)){
                $data['msg'] = "A1005";
                Common::printJson_tj($data,$url,$j);
            }
            $cusign = strtolower(md5($appNo."_".$phcode."_".$tjcode."_".$times."_".self::$secret_key[$tjcode]));
        }
        else
        {
            $key=self::KEY;
            $cusign = md5(date('Y-m-d').$key.$appNo);
        }

        $data = array(
            'success' => false,
            'msg' => array('uid' =>'','apps'=>array())
        );

        //验证合法
        if ($sign=="" || $applist=="" || $appNo=="") {
            $data['msg'] = "K1001";
            Common::printJson_tj($data,$url,$j);
        }
        if (is_numeric($appNo)==false) {
            $data['msg'] = "A1007";
            Common::printJson_tj($data,$url,$j);
        }
        if (strlen($appNo)!=6/* || strlen($phcode)!=15*/) {
            $data['msg'] = "A1002";
            Common::printJson_tj($data,$url,$j);
        }
        if ($appNo>999999 || $appNo<100000) {
            $data['msg'] = "A1011";
            Common::printJson_tj($data,$url,$j);
        }

       if ($sign!=$cusign) {
            $data['msg'] = "A1005";
            Common::printJson_tj($data,$url,$j);
        }

        $rstmodel = RomSoftpak::model()->find('serial_number=:serial_number',array(':serial_number'=>$appNo));
        if (is_null($rstmodel)) {
            $data['msg'] = "A1003";
            Common::printJson_tj($data,$url,$j);
        }

        if ($phcode=="") {
            //记录这个参数为空的接口传递信息
            //创建参数为空的手机记录，此手机不能再次开启该业务
            $applistnull=explode(',',$applist);
            foreach($applistnull as $anull)
            {
                $rmodel=new RomAppresource();
                $rmodel->uid=$rstmodel->uid;
                $rmodel->type=$anull;
                $rmodel->imeicode=$phcode;
                $rmodel->simcode=$simcode;
                $rmodel->tjcode=$tjcode;
                $rmodel->status=0;
                $rmodel->model=$model;
                $rmodel->brand=$brand;
                $rmodel->sys=$sys;
                $rmodel->finishstatus=0;
                $rmodel->from=$from;
                $rmodel->ip=Common::getIp();
                $rmodel->closeend=date("Y-m-d H:i:s");
                $rmodel->createtime=date("Y-m-d H:i:s");
                $rmodel->createstamp=strtotime(date("Y-m-d"));
                if($anull=='weixin')
                {
                    $rmodel->closeend=date("Y-m-d H:i:s");
                }
                $rmodel->insert();
            }
            $data['msg'] = "K1001";
            Common::printJson_tj($data,$url,$j);
        }
        if ($rstmodel->closed==1)
        {
            $data['msg'] = "A1004";
            Common::printJson_tj($data,$url,$j);
        }


        //验证通过，回传数据给接口--{"success":xxxx,"msg":{"uid":"xxxx","apps":[{"name":"xxxx","pakname":"xxxx","key":"xxxx","result":"xxxx"}]}}
        $uid=$rstmodel->uid;
        $member=Member::model()->getById($uid);
        //分组id
        $groupid=$member["agent"];

        $restypes="";
        //用户开启业务列表 $useresource--未开启业务不统计，不计费
        $useresource=MemberResource::model()->findAll('uid=:uid',array('uid'=>$uid));

        if(empty($useresource))
        {
            //返回json数据
            $data['msg'] = "K1002";
            Common::printJson_tj($data,$url,$j);
        }
        foreach($useresource as $ur=>$val)
        {
            //手机开启业务--用户开启业务对比
            $enablev="1";
            if(in_array($val['type'],explode(',',$applist)))
            {
                //用户业务封号--返回卸载----20161212继续上报
/*                if($val["status"]==0)
                {
                    $restypes[$ur]['name']=$val['type'];//业务名称
                    $restypes[$ur]['result']=$enablev;//1卸载此软件、0监视此软件
                    continue;
                }*/
                //用户必须开启业务才可以上报--20161212继续上报
                //if($val["openstatus"]==0) continue;
                //补位处理
                if(substr($phcode,-1,1)==1 && strlen($phcode)==15){
                    //根据长的查询长短对照表
                    $sql="select short_imei from `app_short_imei` where long_imei='{$phcode}'";
                    $result=yii::app()->db->createCommand($sql)->queryAll();
                    if($result){
                        /********* 看是否需要判断品牌、型号以确定是正常还是补位手机********/
                        //存在替换短的
                        $phcode=$result[0]['short_imei'];
                    }
                }
                //判断业务是否需要监控--只要曾有机器安装业务记录就判定无效（imei码+status=1(激活+封号状态下)等做判定）
                $romstatus=RomAppresource::model()->find('type=:type and imeicode=:imeicode',array(':type'=>$val['type'],':imeicode'=>$phcode));

                $data_bu=$this->cover($romstatus,$phcode,$tjcode,$val['type']);//补位处理

                /*2017-11-01安装表之前出现的uid，在此存储在一个变量$before_uid里，因为下面修改了他的值*/
                $before_uid=!empty($romstatus) ? $romstatus->uid : '';
                if(!empty($romstatus))
                {
                    //已激活或是已封号--20161212继续上报
/*                    if($romstatus["status"]==0)
                    {
                        //填写返回JSON值
                        $restypes[$ur]['name']=$val['type'];//业务名称
                        $restypes[$ur]['result']=0;//1卸载此软件、0监视此软件
                    }*/
                    //未激活、未封号、正在监视状态则可以继续监视此手机业务（20151207妖娇确定）---已激活或是已封号--20161212继续上报
                    if(!empty($romstatus))
                    {
                        $simcode_old=$romstatus->simcode;
                        $romstatus->simcode=$simcode;
                        //填写返回JSON值
                        $enablev="0";
                        $appinfo=ProductList::model()->findAll('type=:type and status=:status and agent=:agent order by id asc limit 20',array(':type'=>$val['type'],':agent'=>$groupid,':status'=>1));
                        //下架业务
                        if(empty($appinfo))
                        {
                            $restypes[$ur]['name']=$val['type'];
                            $restypes[$ur]['pakname']="";
                            $restypes[$ur]['key']="";
                            $restypes[$ur]['result']=1;
                        }
                        else
                        {
                            $restypes[$ur]['name']=$val['type'];//业务名称
                            $restypes[$ur]['result']=$enablev;//1卸载此软件、0监视此软件

                            //业务有多个包，判断此md5和包名与手机内业务md5和包名相同否，相同则继续接口
                            if(count($appinfo)>=3)
                            {
                                //兼容老版本key，pakname
                                $restypes[$ur]['key']=$appinfo[0]['sign'];
                                $restypes[$ur]['pakname']=$appinfo[0]['pakname'];
                                //兼容老版本key2，pakname2
                                $restypes[$ur]['key2']=$appinfo[1]['sign'];
                                $restypes[$ur]['pakname2']=$appinfo[1]['pakname'];

                                //第三个包及以上处理--新版2016-08-23
                                foreach($appinfo as $ar=>$av)
                                {
                                    if($ar>1)
                                    {
                                        $restypes[$ur]["ninfo"][]['key']=$av['sign'];
                                        $restypes[$ur]["ninfo"][]['pakname']=$av['pakname'];
                                    }

                                }

                            }
                            else
                            {
                                //兼容老版本，key和key2
                                $restypes[$ur]['key']=$appinfo[0]['sign'];
                                $restypes[$ur]['pakname']=$appinfo[0]['pakname'];

                                //业务有第二个包，如果有值--手机端再次判断此md5与手机内业务md5相同否，key/key2如有相同则在J3中上传此值
                                if(count($appinfo)==2)
                                {
                                    $restypes[$ur]['key2']=$appinfo[1]['sign'];
                                    $restypes[$ur]['pakname2']=$appinfo[1]['pakname'];
                                }
                            }



                            //首次联网运行tongjiapp上报安装
                            if(in_array($count,array(1,3)))
                            {
                                
                                /*2017-11-06 封号 start*/
                                $sql="select id from `app_blacklist` where imeicode='{$phcode}'";
                                $result=yii::app()->db->createCommand($sql)->queryAll();
                                if(!empty($result)){
                                    $romstatus->status=0;
                                    $romstatus->closeend=date('Y-m-d H:i:s');
                                }
                                /*2017-11-06 end*/
                                $firettime=$romstatus->createtime;
                                /*在数据更新之前存入数据库*/

                                //用户更换手机
                                if($before_uid!=$uid){
                                    //查看表repeatinstall_uid中有没有存当前uid第一次安装插入的数据
                                    $sql="select id from `app_rom_repeatinstall_uid` where imeicode='".$phcode."' and before_uid=0";
                                    $result=yii::app()->db->createCommand($sql)->queryAll();

                                    if(empty($result)){
                                        //第一次安装的数据
                                        $romstatus->simcode=$simcode_old;
                                        $romstatus->installtime=$firettime;
                                        $romstatus->createtime=date('Y-m-d H:i:s');
                                        Common::repeatinstall_uid($romstatus,$from,0);
                                        $romstatus->simcode=$simcode;
                                        $romstatus->uid=$uid;
                                        $romstatus->installtime=date('Y-m-d H:i:s');
                                        Common::repeatinstall_uid($romstatus,$from,$before_uid);
                                    }else{
                                        $sql="select id from `app_rom_repeatinstall_uid` where uid={$uid} and imeicode='{$phcode}'";
                                        $add=yii::app()->db->createCommand($sql)->queryAll();
                                        if(empty($add)){
                                            $romstatus->installtime=date('Y-m-d H:i:s');
                                            $romstatus->createtime=date('Y-m-d H:i:s');
                                            $romstatus->uid=$uid;
                                            Common::repeatinstall_uid($romstatus,$from,$before_uid);
                                        }
                                    }
                                }

                                //补位处理
                                if($data_bu['flag']){
                                    $romstatus->status=0;
                                    $romstatus->closeend=date("Y-m-d H:i:s");
                                }

                                //第二次安装上报
                                $romstatus->simcode=$simcode;
                                $romstatus->createtime=$firettime;
                                $romstatus->installtime=date("Y-m-d H:i:s");
                                $romstatus->tjcode = $tjcode;
                                $romstatus->from = $from;
                                $romstatus->uid= $uid;

                                $romstatus->installcount=$romstatus->installcount+1+$data_bu['installcount'];
                                $romstatus->ip=Common::getIp();
                                $romstatus->update();

                                Common::repeatInstall($romstatus,$from);//重复安装 2017-11-30 正式上线
                                //$romstatus更新后的数据，第二个参数是uptype

                            }

                        }

                    }

                }
                else
                {
                    //创建需要监控的--手机-业务关系表
                    $rmodel=new RomAppresource();
                    $rmodel->uid=$uid;
                    $rmodel->type=$val['type'];
                    $rmodel->imeicode=$phcode;
                    $rmodel->simcode=$simcode;
                    $rmodel->tjcode=$tjcode;
                    /*2017-11-06 封号 start*/
                    $sql="select id from `app_blacklist` where imeicode='{$phcode}'";
                    $result=yii::app()->db->createCommand($sql)->queryAll();
                    /*2017-11-06 end*/
                    if($val["status"]==0 || !empty($result))
                    {
                        $rmodel->status=0;
                        $rmodel->closeend=date("Y-m-d H:i:s");
                    }
                    else
                    {
                        $rmodel->status=1;
                    }
                    if($val['type']=='weixin')
                    {
                        $rmodel->status=0;
                        $rmodel->closeend=date("Y-m-d H:i:s");
                    }
                    //补位处理
                    if($data_bu['flag']){
                        $rmodel->status=0;
                        $rmodel->closeend=date("Y-m-d H:i:s");
                    }
                    $rmodel->model=$model;
                    $rmodel->brand=$brand;
                    $rmodel->sys=$sys;
                    $rmodel->finishstatus=0;
                    $rmodel->createtime=date("Y-m-d H:i:s");
                    $rmodel->createstamp=strtotime(date("Y-m-d"));
                    $rmodel->installtime=date("Y-m-d H:i:s");
                    $rmodel->installcount=1+$data_bu['installcount'];
                    $rmodel->from=$from;
                    $rmodel->ip=Common::getIp();

                    if(!is_null($data_bu['romstatus_bu'])){
                        //补位封号，合并历史记录
                        $rmodel->installcount=1+$data_bu['romstatus_bu']->installcount;

                        $rmodel->createtime=$data_bu['romstatus_bu']->createtime;
                        $rmodel->createstamp=$data_bu['romstatus_bu']->createstamp;
                        $rmodel->finishstatus=$data_bu['romstatus_bu']->finishstatus;
                        $rmodel->finishdate=$data_bu['romstatus_bu']->finishdate;
                        $rmodel->finishtime=$data_bu['romstatus_bu']->finishtime;
                        $rmodel->is_check=$data_bu['romstatus_bu']->is_check;
                        $rmodel->phcode=$data_bu['romstatus_bu']->phcode;

                    }
                    $rmodel->insert();

                    Common::repeatInstall($rmodel,$from);//首次安装也进入重复安装 2017-11-30 正式上线
                    $enablev="0";

                    //填写返回JSON值
                    $appinfo=ProductList::model()->findAll('type=:type and status=:status and agent=:agent order by id asc limit 20',array(':type'=>$val['type'],':agent'=>$groupid,':status'=>1));
                    //下架业务
                    if(empty($appinfo))
                    {
                        $restypes[$ur]['name']=$val['type'];
                        $restypes[$ur]['pakname']="";
                        $restypes[$ur]['key']="";
                        $restypes[$ur]['result']=1;
                    }
                    else
                    {
                        $restypes[$ur]['name']=$val['type'];//业务名称
                        $restypes[$ur]['result']=$enablev;//1卸载此软件、0监视此软件

                        //业务有多个包，判断此md5和包名与手机内业务md5和包名相同否，相同则继续接口
                        if(count($appinfo)>=3)
                        {
                            //兼容老版本key，pakname
                            $restypes[$ur]['key']=$appinfo[0]['sign'];
                            $restypes[$ur]['pakname']=$appinfo[0]['pakname'];
                            //兼容老版本key2，pakname2
                            $restypes[$ur]['key2']=$appinfo[1]['sign'];
                            $restypes[$ur]['pakname2']=$appinfo[1]['pakname'];

                            //第三个包及以上处理--新版2016-08-23
                            foreach($appinfo as $ar=>$av)
                            {
                                if($ar>1)
                                {
                                    $restypes[$ur]["ninfo"][]['key']=$av['sign'];
                                    $restypes[$ur]["ninfo"][]['pakname']=$av['pakname'];
                                }

                            }

                        }
                        else
                        {
                            //兼容老版本，key和key2
                            $restypes[$ur]['key']=$appinfo[0]['sign'];
                            $restypes[$ur]['pakname']=$appinfo[0]['pakname'];

                            //业务有第二个包，如果有值--手机端再次判断此md5与手机内业务md5相同否，key/key2如有相同则在J3中上传此值
                            if(count($appinfo)==2)
                            {
                                $restypes[$ur]['key2']=$appinfo[1]['sign'];
                                $restypes[$ur]['pakname2']=$appinfo[1]['pakname'];
                            }
                        }
                    }



                }

            }
        }
        //返回json数据
        if(!empty($restypes))
        {
            $data['success'] = true;
            $data['msg']['uid'] = $uid;//用户id

            //$restypes数组重新定义key
            $i = 0;
            foreach ($restypes as $k1=>$v1)
            {
                $b[$i] = $restypes[$k1];
                $i++;
            }
            $restypes = $b;

            $data['msg']['apps'] = $restypes;
            Common::printJson_tj($data,$url,$j);
        }
        else
        {
            $data['success'] = true;
            $data['msg'] = "K1004";
            Common::printJson_tj($data,$url,$j);
        }

        exit;
    }
    /*
    * @name 补位imei码处理方法
    */
    protected function cover($romstatus,$imei,$tjcode,$type){
        //1.imei码长度判断
        $flag=false;
        $installcount=0;
        $romstatus_bu=null;
        if(strlen($imei)<15){
            $bu_imei=Common::getStrLength($imei);//获取补位imei码

            //根据短的查询长短对照表
            $sql="select id from `app_short_imei` where short_imei='{$imei}'";
            $result=yii::app()->db->createCommand($sql)->queryAll();
            if($result){
                a:
                //对照表存在
                //判断短imei码是否有安装数据
                //$romstatus=RomAppresource::model()->find('type=:type and imeicode=:imeicode',array(':type'=>$type,':imeicode'=>$imei));
                if($romstatus){
                    //存在短
                    $romstatus_bu=RomAppresource::model()->find('type=:type and imeicode=:imeicode',array(':type'=>$type,':imeicode'=>$bu_imei));
                    if($romstatus_bu){
                        //存在补位imei码数据 封号判断
                        if($romstatus_bu->status==0){
                            //补位封号：短的imei码并封号判断
                            /* if($romstatus->status==0 && $romstatus_bu->closeend !='0000-00-00 00:00:00'){
                                //短的imei码封号
                                //短的封号安装上报

                            }else{
                                //短的imei码未封号
                                //短的安装上报

                            }*/
                            $romstatus_bu=null;
                        }else{
                            //补位未封号：短的imei码并封号判断
                            if($romstatus->status==0 ){
                                //短的封号：1补位imei码封号
                                $romstatus_bu->status=0;
                                $romstatus_bu->closeend=date('Y-m-d H:i:s');
                                $romstatus_bu->update();
                                //短的封号：2.短的封号安装上报
                                $romstatus_bu=null;
                            }else{
                                //短的正常：1补位imei码封号
                                $installcount=$romstatus_bu->installcount;//补位安装次数
                                $romstatus_bu->status=0;
                                $romstatus_bu->closeend=date('Y-m-d H:i:s');
                                $romstatus_bu->update();
                                //短的正常：2.补位安装上报合并到短的上 $installcount
                            }
                        }

                    }else{
                        //不存在补位imei码和短imei码安装数据
                        //短的首次安装上报

                    }
                    $data=array("imei"=>$imei,'flag'=>$flag,'installcount'=>$installcount,'romstatus_bu'=>$romstatus_bu);
                }else{
                    //不存在短 查询补位安装数据
                    $romstatus_bu=RomAppresource::model()->find('type=:type and imeicode=:imeicode',array(':type'=>$type,':imeicode'=>$bu_imei));
                    if($romstatus_bu){
                        //存在补位imei码数据 封号判断
                        if($romstatus_bu->status==0){
                            //封号：新建短的并封号
                            $flag=true;
                        }else{
                            //未封号：1.补位封号
                            $installcount=$romstatus_bu->installcount;//补位安装次数
                            $romstatus_bu->status=0;
                            $romstatus_bu->closeend=date('Y-m-d H:i:s');
                            $romstatus_bu->update();
                            //2.新增短的（导入补位的历史记录） $romstatus_bu

                        }

                    }else{
                        //不存在补位imei码数据
                        //短的首次安装上报
                    }
                    $data=array("imei"=>$imei,'flag'=>$flag,'installcount'=>$installcount,'romstatus_bu'=>$romstatus_bu);
                }
            }else{
                //不存在 保存
                $sql="INSERT INTO `app_short_imei`(`id`,`short_imei`,`long_imei`,`createtime`)VALUES
                        ('','".$imei."','".$bu_imei."','".date('Y-m-d H:i:s')."')";
                Yii::app()->db->createCommand($sql)->execute();
                goto a;
            }
        }else{
            //判断版本、末尾1
            //$str=substr($imei,-1,1);
            if(substr($imei,-1,1)==1){
                //根据长的查询长短对照表
                $sql="select short_imei from `app_short_imei` where long_imei='{$imei}'";
                $result=yii::app()->db->createCommand($sql)->queryAll();
                if($result){
                    /********* 看是否需要判断品牌、型号以确定是正常还是补位手机********/
                    //存在替换短的
                    $imei=$result[0]['short_imei'];
                }
            }
            $data=array("imei"=>$imei,'flag'=>$flag,'installcount'=>$installcount,'romstatus_bu'=>$romstatus_bu);
        }
        return $data;
    }

    /**
     *  ROM：J3-上报数据----2小时上报
     *  string $phcode imei码
     *  string $simcode sim卡
     *  string $com：运营商
     *  string $runlength：运行时长（单位分，不足一分按一分算）
     *  string $runcount：运行次数
     *  string $runtime：运行时间点
     *  string $date：数据当天日期
     *  string $sys：系统版本
     *  string $type：卸载-1
     *  string $tjcode：统计内码
     *  string $appname：业务app名称
     * https://www.sutuiapp.com/api/UploadData?appNo=100001&tjcode=6&watch=[{%22appname%22:%22meituan%22,%22appmd5%22:%225B60BD44D621F4EFEB6D5FEAF1A03A33%22,%22type%22:%221%22,%22runlength%22:%2256%22,%22runcount%22:%222%22,%22runtime%22:%228%22,%22date%22:%222016-12-08%22}]&phcode=123456128946456&simcode=34werwre&com=chinamobile&sign=dcb6806d828e2f9226501df471cb4cb3&sys=andriod4.2&mac=D4:3D:7E:04:F7:18&model=meipai&uid=1&from=0&brand=%E5%B0%8F%E7%B1%B3
     */
    public function actionUploadData()
    {
        $j="J3";
        $appNo=Yii::app()->request->getParam('appNo');
        $sign=trim(Yii::app()->request->getParam('sign'));
        $from=Yii::app()->request->getParam('from');
        if(empty($from)) $from=0;
        $phcode=Yii::app()->request->getParam('phcode');
        $simcode=Yii::app()->request->getParam('simcode');
        $tjcode=Yii::app()->request->getParam('tjcode');
        $sys=Yii::app()->request->getParam('sys');
        $mac=Yii::app()->request->getParam('mac');
        $model=Yii::app()->request->getParam('model');
        $brand=Yii::app()->request->getParam('brand','');
        $uid=Yii::app()->request->getParam('uid');
        $com=Yii::app()->request->getParam('com');
        $watch=Yii::app()->request->getParam('watch');
        $times=trim(Yii::app()->request->getParam('times'));//加密时间戳

        /*$sqlt="INSERT INTO `app_rom_appupdata001` ( `uid`, `simcode`, `sys`, `appname`, `imeicode`, `tjcode`, `model`, `brand`,  `createtime`, `from`) VALUES
('" . $uid. "','" . $simcode. "','" . $sys. "','" . $watch. "','" . $phcode. "','" . $tjcode. "','" . $model. "','" . $brand. "','" . date('Y-m-d H:i:s'). "','" . $from. "')";
        Yii::app()->db->createCommand($sqlt)->execute();*/
        $url="";
        //$url="appNo:".$appNo."-sign:".$sign."-phcode:".$phcode."-simcode:".$simcode."-sys:".$sys."-model:".$model."-mac:".$mac."-uid:".$uid."-com:".$com."-watch:".$watch."-tjcode:".$tjcode;

        //$tjcode>=7开始使用新的加密方式--20160902
        if($tjcode>=7 && !empty($times))
        {
            if(!array_key_exists($tjcode,self::$secret_key)){
                $data['msg'] = "A1005";
                Common::printJson_tj($data,$url,$j);
            }
            $cusign = strtolower(md5($phcode."_".$appNo."_".$tjcode."_".$times."_".self::$secret_key[$tjcode]));
        }
        else
        {
            $key=self::KEY;
            $cusign = md5($key.date('Y-m-d').$appNo);
        }

        $data = array(
            'success' => false,
            'msg' => array('apps'=>array())
        );

        //验证合法
        if ($appNo=="" || $sign=="" || $uid==""/* || $phcode==""*/) {
            $data['msg'] = "K1001";
            Common::printJson_tj($data,$url,$j);
        }
        if (is_numeric($appNo)==false) {
            $data['msg'] = "A1007";
            Common::printJson_tj($data,$url,$j);
        }

        if (strlen($appNo)!=6) {
            $data['msg'] = "A1002";
            Common::printJson_tj($data,$url,$j);
        }
        if ($appNo>999999 || $appNo<100000) {
            $data['msg'] = "A1011";
            Common::printJson_tj($data,$url,$j);
        }
        if ($mac!="" && strlen($mac) != 17) {
            $data['msg'] = "C1002";
            Common::printJson_tj($data,$url,$j);
        }
        $rmodel = RomSoftpak::model()->find('serial_number=:serial_number',array(':serial_number'=>$appNo));
        if (is_null($rmodel)) {
            $data['msg'] = "A1003";
            Common::printJson_tj($data,$url,$j);
        }
       if ($sign!=$cusign) {
            $data['msg'] = "A1005";
            Common::printJson_tj($data,$url,$j);
        }
        if ($rmodel->closed==1) {
            $data['msg'] = "A1004";
            Common::printJson_tj($data,$url,$j);
        }
        $uids=$rmodel->uid;
        if ($uids!=$uid) {
            $data['msg'] = "C1005";
            Common::printJson_tj($data,$url,$j);
        }

        //验证通过，回传数据给接口--{"success":true,"msg":{"apps":[{"appname":"xxxx","result":"xxxx"},{"appname":"xxxx","result":"xxxx"}]}}
        $restypes="";
        $result="0";//0正常需要上报、1作弊用户卸载此软件、2已完成激活停止监视
        //业务分组数据
        //$watch = '[{"appname":"jrtt","runlength":"16","runcount":"2","runtime":"2015-10-29 00:00:00","date":"2015-10-29"},{"appname":"taobao","runlength":"56","runcount":"2","runtime":"8","date":"2015-10-29 00:00:00"}]';
        if ($watch=="")
        {
            //返回json数据
            $data['success'] = true;
            $data['msg'] = "C1008";
            Common::printJson_tj($data,$url,$j);
            exit;
        }
        else
        {
            $watch = object_array(json_decode($watch));
        }

        if(!empty($watch))
        {
            //用户开启业务列表 $useresource--业务关闭可以上报，封号不可上报（用户业务封号--返回卸载）
            $useresource=MemberResource::model()->findAll('uid=:uid',array('uid'=>$uid));
            if(empty($useresource))
            {
                //返回json数据
                $data['msg'] = "K1002";
                Common::printJson_tj($data,$url,$j);
            }

            foreach($watch as $wkey=>$wval)
            {
                $appname=$wval["appname"];
                $runlength=$wval["runlength"];
                $runcount=$wval["runcount"];
                $runtime=$wval["runtime"];
                $type=$wval["type"];
                $date=$wval["date"];
                $appmd5=$wval["appmd5"];
                //统计软件自身12小时上报取消
                if($type == 2)
                {
                    continue;
                }
                //2345tqw业务停止上报
                if($appname == "2345tqw")
                {
                    continue;
                }
                //填写返回JSON值
                $appinfoup=Product::model()->find('pathname=:pathname and status=:status',array(':pathname'=>$appname,':status'=>0));
                //下架业务
                if(!empty($appinfoup))
                {
                    continue;
                }

                if($runlength<=0 || $runcount<=0)
                {
                    //返回json数据
                    $data['msg'] = "C1008";
                    Common::printJson_tj($data,$url,$j);
                }
                if(empty($appmd5) || $appmd5=="")
                {
                    //返回json数据
                    $data['msg'] = "C1008";
                    Common::printJson_tj($data,$url,$j);
                }

                if(!empty($useresource))
                {
                    $count=count($useresource);
                    foreach($useresource as $ur=>$val)
                    {
                        //用户开启业务--上报业务对比,$type==2为统计软件自身上报数据
                        if($appname==$val['type'] || ($ur+1 == $count && $type==2))
                        {
                            //无sim卡不上报数据，告诉手机正常需上报（监视）$type==2为统计软件自身上报数据
                            if((empty($simcode) || $simcode=="") && $type!=2)
                            {
                                $restypes[$wkey]['appname']=$appname;
                                $restypes[$wkey]['result']="0";
                                continue;
                            }

                            //包md5错误
                            $prolist=ProductList::model()->find('sign=:sign and status=:status and type=:type',array(':type'=>$appname,':sign'=>$appmd5,':status'=>1));
                            if(empty($prolist) && $appmd5!="9999")
                            {
                                $restypes[$wkey]['appname']=$appname;
                                $restypes[$wkey]['result']="1";
                                continue;
                            }
                            //补位处理
                            if(substr($phcode,-1,1)==1){
                                //根据长的查询长短对照表
                                $sql="select short_imei from `app_short_imei` where long_imei='{$phcode}'";
                                $result=yii::app()->db->createCommand($sql)->queryAll();
                                if($result){
                                    /********* 看是否需要判断品牌、型号以确定是正常还是补位手机********/
                                    //存在替换短的
                                    $phcode=$result[0]['short_imei'];
                                }
                            }
                            //如果表中不存在此imeicode，则判断作弊
                            $remodel=RomAppresource::model()->find('type=:type and imeicode=:imeicode',array(':type'=>$appname,':imeicode'=>$phcode));
                            if(empty($remodel) && $type!=2)
                            {
                                //返回json数据
                                $data['msg'] = "C1007";
                                Common::printJson_tj($data,$url,$j);
                            }


                            //封号不可上报（用户业务封号--返回卸载）--20161212可上报日志表60d
                            if($val["status"]==0)
                            {
                                $status=1;//封号
                                $applog=$this->actionAppupdataLog($uid,$simcode,$sys,$mac,$phcode,$tjcode,$model,$appname,$runlength,$runcount,$type,$appmd5,$status,$from);
                                $restypes[$wkey]['appname']=$applog[0];
                                $restypes[$wkey]['result']=$applog[1];
                                continue;

                            }


                            //上报数据状态开始
                            //未完成激活--处理,//完成激活，卸载上报可以$type==1
                            //if(($remodel["finishstatus"]==0) || ($remodel["finishstatus"]==1 && $type==1) || ($type==2))
                            if(($remodel["finishstatus"]==0) || ($type==2))
                            {
                                //判断符合完成激活规则--$result="2"--未做处理

                                //判断是作弊用户--封号状态（此手机业务封号，并非用户业务封号）--20161212可上报日志表60d
                                if(($remodel["closeend"]!="0000-00-00 00:00:00")  && ($type!=2))
                                {
                                    $status=1;//封号
                                    $applog=$this->actionAppupdataLog($uid,$simcode,$sys,$mac,$phcode,$tjcode,$model,$appname,$runlength,$runcount,$type,$appmd5,$status,$from);
                                    $restypes[$wkey]['appname']=$applog[0];
                                    $restypes[$wkey]['result']=$applog[1];
                                    continue;

                                }
                                else
                                {
                                    //未完成激活--需上报
                                    if($uid!=$remodel["uid"]){$uid=$remodel["uid"];}
                                    $upmodel = new RomAppupdata();
                                    $upmodel->uid = $uid;
                                    $upmodel->simcode = $simcode;
                                    $upmodel->sys = $sys;
                                    $upmodel->imeicode = $phcode;
                                    $upmodel->tjcode = $tjcode;
                                    $upmodel->mac = $mac;
                                    $upmodel->com = $com;
                                    $upmodel->model = $model;
                                    $upmodel->brand = $brand;
                                    $upmodel->appname = $appname;
                                    $upmodel->runlength = $runlength;
                                    $upmodel->runcount = $runcount;
                                    $upmodel->runtime = $runtime;
                                    $upmodel->type = $type;
                                    $upmodel->date = $date;
                                    $upmodel->appmd5 = $appmd5;
                                    $upmodel->from = $from;
                                    $upmodel->createtime = date("Y-m-d H:i:s");
                                    $upmodel->ip=Common::getIp();
                                    $upmodel->insert();

                                    //from=5新地推：修改相关安装数据
                                    if($from==5)
                                    {
                                        if($remodel["tc"]==1)
                                        {
                                            if(isset($remodel["noincome"]) && ($remodel["noincome"]==0))
                                            {

                                            }
                                            else
                                            {
                                                if(isset($remodel["tcid"]) && $remodel["tcid"]<1000)
                                                {
                                                    //查找是否已有到达标记
                                                    $tcmodel=RomAppresource::model()->find('0<tcid<1000 and imeicode=:imeicode and noincome!=0 and tc=1 and tcfirsttime!=""',array(':imeicode'=>$phcode));
                                                    if(empty($tcmodel))
                                                    {
                                                        $remodel->tcfirsttime=date("Y-m-d H:i:s");
                                                        $remodel->update();
                                                    }
                                                }
                                            }

                                        }
                                    }

                                    //补充安装时没有sim记录
                                    if(empty($remodel["simcode"]))
                                    {
                                        $remodel->simcode=$simcode;
                                        $remodel->update();
                                    }

                                    //20170829未激活数据可上报日志表60d
                                    $status=0;//未封号
                                    $applog=$this->actionAppupdataLog($uid,$simcode,$sys,$mac,$phcode,$tjcode,$model,$appname,$runlength,$runcount,$type,$appmd5,$status,$from);

                                    //type=2不返回相应结果
                                    $restypes[$wkey]['appname']=$appname;

                                    if($type==1)
                                    {
                                        $restypes[$wkey]['result']="1";
                                    }
                                    else
                                    {
                                        $restypes[$wkey]['result']="0";
                                    }

                                }
                            }
                             //完成激活，停止监视，//完成激活，卸载上报可以//
                            else
                            {
                                //$restypes[$wkey]['result']="2";--20161212原判定激活后返回2,为只统计卸载上报，现需要继续监视
                                //20161212激活后数据可上报日志表60d
                                $status=0;//未封号
                                $applog=$this->actionAppupdataLog($uid,$simcode,$sys,$mac,$phcode,$tjcode,$model,$appname,$runlength,$runcount,$type,$appmd5,$status,$from);
                                $restypes[$wkey]['appname']=$applog[0];
                                $restypes[$wkey]['result']=$applog[1];
                                continue;
                            }

                            //break;
                        }
                    }
                }


            }

            //返回json数据
            $data['success'] = true;

            //$restypes数组重新定义key
            $i = 0;
            $b=array();
            if(!empty($restypes))
            {
                //同一业务上报多次记录，返回合并后的值
                $unarray=array();
                foreach ($restypes as $ka=>$va)
                {
                    $appnames=$va["appname"];
                    foreach ($restypes as $kb=>$vb)
                    {
                        if($ka!=$kb &&$ka<$kb && $appnames==$vb["appname"])
                        {
                            $restypes[$ka]["result"]=$va["result"]+$vb["result"];
                            $restypes[$ka]["result"]="".$restypes[$ka]["result"]."";
                            $unarray[]=$kb;
                        }
                    }
                }
                if(!empty($unarray))
                {
                    foreach ($unarray as $ua=>$na)
                    {
                        unset($restypes[$na]);
                    }
                }

                //$restypes数组重新定义key
                foreach ($restypes as $k1=>$v1)
                {
                    $b[$i] = $restypes[$k1];
                    $i++;
                }
                $restypes = $b;
            }


            $data['msg']['apps'] = $restypes;
             Common::printJson_tj($data,$url,$j);

        }

        exit;
    }

    /**
     * ROM：J4-软件升级---升级后继续之前的接口处理（判断之前走过几个接口，继续接口业务）
     * string $appNo 序列号
     * string $version：版本号
     * https://www.sutuiapp.com/api/UpdatePak?appNo=100001&version=1.0.1&sign=e369bd553a6677549b916ee0ee761565
     */
    public function actionUpdatePak()
    {
        $j="J4";
        $appNo=Yii::app()->request->getParam('appNo');
        $sign=Yii::app()->request->getParam('sign');
        $version=Yii::app()->request->getParam('version');
        $url="appNo:".$appNo."-sign:".$sign."-version:".$version;

        $key=self::KEY;
        $cusign = md5($key.date('Y-m-d').$appNo);
        $data = array(
            'success' => false,
            'msg' => array()
        );

        //验证合法
        if ($appNo=="" || $sign=="" || $version=="") {
            $data['msg'] = "K1001";
            Common::printJson_tj($data,$url,$j);
        }
        if (is_numeric($appNo)==false) {
            $data['msg'] = "A1007";
            Common::printJson_tj($data,$url,$j);
        }
        if (strlen($appNo) != 6) {
            $data['msg'] = "A1002";
            Common::printJson_tj($data,$url,$j);
        }
        $model = RomSoftpak::model()->find('serial_number=:serial_number',array(':serial_number'=>$appNo));
        if (is_null($model)) {
            $data['msg'] = "A1003";
            Common::printJson_tj($data,$url,$j);
        }
        if ($sign!=$cusign) {
            $data['msg'] = "A1005";
            Common::printJson_tj($data,$url,$j);
        }
        if ($model->closed==1) {
            $data['msg'] = "A1004";
            Common::printJson_tj($data,$url,$j);
        }

        //判断当前版本和传递的版本号是否一致
        $data['success'] = true;
        $data['msg']['version'] = $model->version;//与当前版本号对比--相等则跳过、不等则根据url升级
        $data['msg']['url'] = "http://www.sutuiapp.com".$model->url;//为空则跳过升级
        Common::printJson_tj($data,$url,$j);

    }
    /**
     * 上报数据到日志表
     * */
    public function actionAppupdataLog($uid,$simcode,$sys,$mac,$phcode,$tjcode,$model,$appname,$runlength,$runcount,$type,$appmd5,$status,$from)
    {
        $first_data=RomAppupdatalog::model()->find('imeicode=:imeicode and appname=:appname and first=:first',array(':imeicode'=>$phcode,':appname'=>$appname,':first'=>1));
        if(empty($first_data))
        {
            //记录
            $first=1;
            $this->actionAppLog($uid,$simcode,$sys,$mac,$phcode,$tjcode,$model,$appname,$runlength,$runcount,$type,$appmd5,$first,$status,$from);
            return array($appname,"0");
        }
        elseif(!empty($first_data) && $this->diffBetweenTwoDays(date("Y-m-d",strtotime($first_data["createtime"])),date("Y-m-d"))<=60)
        {
            $first=0;
            if($type==1)
            {
                //记录
                $this->actionAppLog($uid,$simcode,$sys,$mac,$phcode,$tjcode,$model,$appname,$runlength,$runcount,$type,$appmd5,$first,$status,$from);
                return array($appname,"1");
            }
            else
            {
                $curr_data=RomAppupdatalog::model()->find('imeicode=:imeicode and appname=:appname and first!=:first and createtime like "%'.date("Y-m-d").'%"',array(':imeicode'=>$phcode,':appname'=>$appname,':first'=>1));

                //记录
                if(empty($curr_data))
                {
                    $this->actionAppLog($uid,$simcode,$sys,$mac,$phcode,$tjcode,$model,$appname,$runlength,$runcount,$type,$appmd5,$first,$status,$from);
                }
                return array($appname,"0");
            }
        }
        elseif(!empty($first_data) && $this->diffBetweenTwoDays(date("Y-m-d",strtotime($first_data["createtime"])),date("Y-m-d"))>60)
        {
            //日志表超过60天记录返回卸载
            return array($appname,"1");
        }
    }

    /**
     * 上报数据到日志表
     * */
    public function actionAppLog($uid,$simcode,$sys,$mac,$phcode,$tjcode,$model,$appname,$runlength,$runcount,$type,$appmd5,$first,$status,$from){
        $upmodel = new RomAppupdatalog();
        $upmodel->uid = $uid;
        $upmodel->simcode = $simcode;
        $upmodel->sys = $sys;$upmodel->mac = $mac;
        $upmodel->imeicode = $phcode;
        $upmodel->tjcode = $tjcode;
        $upmodel->model = $model;
        $upmodel->appname = $appname;
        $upmodel->runlength = $runlength;
        $upmodel->runcount = $runcount;
        $upmodel->type = $type;
        $upmodel->appmd5 = $appmd5;
        $upmodel->createtime = date("Y-m-d H:i:s");
        $upmodel->first = $first;
        $upmodel->status = $status;
        $upmodel->from = $from;
        $upmodel->ip=Common::getIp();
        $upmodel->insert();
    }

    /**
     * 通过统计id返回用户id和代理id
     * @datetime 2016-5-9 10:58:37
     * @param int $stat_id  //统计id
     * @return json
     * */
    public function actionStatidGetMember($stat_id=0){
        if($stat_id==0){
            //返回错误提示
            $this->ReturnError("参数错误");
        }
        //$resource = MemberResource::model()->find('`key`=:key',array(":key"=>$stat_id));
        $romSoftModel = new RomSoftpak();
        $romSoftData = $romSoftModel -> find("serial_number=:serial_number",array(":serial_number"=>$stat_id));
        if(!$romSoftData){
            //返回错误信息
            $this->ReturnError("资源不存在");
        }
        $memberId = $romSoftData['uid'];
        //通过用户id得到代理id
        $memberData = Member::model()->findByPk($memberId);
        if(!$memberData){
            //返回错误信息
            $this->ReturnError("用户信息不存在");
        }
        $data['status_code'] = 200;
        $data['status_message'] = "success";
        //$data['data'] = array();
        $data['data']['member_id'] = $memberId;
        $data['data']['agent_id'] = $memberData['agent'];
        $this->Array2Json($data);
    }
    /**
     * 通过代理id和产品id获取apk的md5和包名
     * @param int $agent_id
     * @param int $product_id
     * @return json
     * */
    public function actionGetApkPackage($product_id,$agent_id){
        $model = new ProductList();
        $packageData = $model -> find(array("condition"=>"pid=$product_id AND agent=$agent_id AND status=1","order"=>"id DESC","limit"=>"1"));
        if($packageData){
            $data['status_code'] = 200;
            $data['status_message'] = "success";
            //$data['data'] = array();
            $data['data']['package'] = $packageData['pakname'];
            $data['data']['md5'] = $packageData['sign'];
            $this->Array2Json($data);
        }else{
            $this->ReturnError("信息不存在");
        }
    }

    /*
     * 错误提示信息
     * */
    protected function ReturnError($error=""){
        $data = array();
        $data['status_code'] = 400;
        $data['status_message'] = ($error=="")?"参数错误":$error;
        $this->Array2Json($data);
    }
    /*
     * 数组转json
     * */
    protected function Array2Json($data){
        header('Content-type: application/json');
        exit(CJSON::encode($data));
    }
    /*
     * 数组转json
     * */
    protected function ReturnJson($data){
        header('Content-type: application/json');
        exit(CJSON::encode($data));
    }
    //日期相差天数
    protected function diffBetweenTwoDays ($day1, $day2)
    {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return ($second1 - $second2) / 86400;
    }
    //获取服务器日期
    public function actionGetTimer()
    {
        echo date('Y-m-d');
    }

}
 //将json数组转换为标准array
function object_array($array)
{
    if(is_object($array)){
        $array = (array)$array;
    }
    if(is_array($array)){
        foreach($array as $key=>$value){
            $array[$key] = object_array($value);
        }
    }
    return $array;
}

