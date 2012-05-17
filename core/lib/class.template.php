<?php
class Class_Template extends url
{
var $blocks = array();
var $template;
var $Page;

    function Class_Template(&$Page)
    {
        $this->Page = &$Page;
    }
    function tag($name,$content,$block){
        if(!isset($this->blocks[$block])){
            $this->blocks[$block]='';
        }
        $this->blocks[$block]=$this->parseBlocks($this->constTag($name,$content));
    }

    function assign($template, $content, $block)
    {
      if(!isset($this->blocks[$block])){
        $this->blocks[$block]='';
      }
        $this->blocks[$block] = $this->parseBlocks($this->template($template, $content));
    }
     function constTag($name,$content=array()){
         $arr=array('name','class','style','text');
         $tpl="<[+TAGname+][+TAGclass+][+TAGstyle+]>[+TAGtext+]</[+TAGname+]>";
         if($name!=''){
             $content['name']=$name;
         }else{
             return '';
         }

         foreach($content as $item=>$value){
             if(in_array($item,$arr)){
                 $tpl=str_replace("[+TAG".$item."+]",$value,$tpl);
                 unset($arr[$item]);
             }
         }
         foreach($arr as $item){
             $tpl=str_replace("[+TAG".$item."+]",'',$tpl);
         }
         return $tpl;
     }
    function template($template, $content = array())
    {
      $Page = &$this->Page;
      ob_start();
      include('assets/templates/'.$template.'.tpl');
      $data = ob_get_contents();
      ob_end_clean();
      return $data;
    }

    function includeTemplates($template,$memory='')
    {
	$template=$memory!=''?$this->blocks[$memory]:$template;
	 while(strpos($template, '{TEMPLATE:"')!=false)
      {
        @preg_match_all('|TEMPLATE:"([a-zA-Z0-9_\.-]+)"|U', $template, $includes);

        foreach($includes[1] as $include)
        {	
            if (@file_exists("templates/".$include))
            {
				
              $template = str_replace('{TEMPLATE:"'.$include.'"}', $this->template("templates/".$include), $template);
            }
            else
            {
              $template = str_replace('{TEMPLATE:"'.$include.'"}', "not found template ".$include, $template);
            }
        }
      }
	  if($memory!='')
		{
			
			$this->blocks[$memory]=$template;
			
		}
		else
		{
		$this->template = $template;
		}
    }

    function parseBlocks($data='')
    {
       $tpl=($data=='')?$this->template:$data;
      while (strpos($tpl, '{BLOCK:"') !== false)
      {
        preg_match_all('|BLOCK:"([a-zA-Z0-9_\.-]+)"|U', $tpl, $includes);
        foreach($includes[1] as $include)
        {
            if(!isset($this->blocks[$include])){
                $this->blocks[$include]='<!--EMPTY BLOCK '.$include."!-->";
            }
            $tpl = str_replace('{BLOCK:"'.$include.'"}', $this->blocks[$include], $tpl);
        }
      }
        return $tpl;
    }
    function replaceUrl(){
            $tpl=$this->template;
        //{~URL:[+mode=info;do=parser+]~}
        //{~URL:[+0+]~}
            while(strpos($tpl,'{~URL:"')!==false){
                preg_match_all('|{~URL:"(.*)"~}|U',$tpl,$url);
                foreach($url[1] as $link){
                    $arrparam=array();
                   if($link=='0'){
                    $tpl=str_replace('{~URL:"'.$link.'"~}',$this->fullUrl(),$tpl);   //определять подпапку
                   }else{
                       $params=explode(";",$link);
                       foreach($params as $param){
                           $tmp=explode('=',$param);
                           $arrparam[$tmp[0]]=$tmp[1];
                       }
                       $golink=$this->makeUrl($arrparam);
                       if($golink!==false){
                        $tpl=str_replace('{~URL:"'.$link.'"~}',$golink,$tpl);
                       }else{
                            $tpl=str_replace('{~URL:"'.$link.'"~}','#',$tpl);
                       }
                   }

                }
            }
            $this->template=$tpl;
        }
    function render($template)
    {
      $Page = $this->Page;
      $this->includeTemplates($this->template($template));
      $this->template=$this->parseBlocks($this->template);
      $this->replaceUrl();
      return $this->template;
    }
}
?>