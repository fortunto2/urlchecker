<script>
    
    $(document).ready(function($){
        $('.sitelist tbody tr').each(function(){
            var siteUrl=$(this).find("th span.urlsite").attr("id");
             $.ajax({
                url:'{~URL:"mode=parser;do=code"~}?url='+siteUrl,
                type:"GET",
                dataType: 'json',
                beforeSend: function(){
                    $(this).html('<img src="{~URL:"0"~}assets/images/checker.gif" />');
                },
                requestHeaders: {Accept: 'application/json'},
                async:true,
                context:$(this).find('.codesite'),
                error: function(){
                    $(this).text('');
                },
                success: function(data){
                    if(data.error!=''){
                        console.log(data);
                    }
                    $(this).text(data.value);
                }
            });

            $.ajax({
                url:'{~URL:"mode=parser;do=age"~}?url='+siteUrl,
                type:"GET",
                dataType: 'json',
                 beforeSend: function(){
                    $(this).html('<img src="{~URL:"0"~}assets/images/checker.gif" />');
                },
                requestHeaders: {Accept: 'application/json'},
                async:true,
                context:$(this).find('.agesite'),
                error: function(){
                    $(this).text('');
                },
                success: function(data){
                    if(data.error!=''){
                        console.log(data);
                    }
                    $(this).text(data.value);
                }
            });

            $.ajax({
                url:'{~URL:"mode=parser;do=igood"~}?url='+siteUrl,
                type:"GET",
                dataType: 'json',
                 beforeSend: function(){
                    $(this).html('<img src="{~URL:"0"~}assets/images/checker.gif" />');
                },
                requestHeaders: {Accept: 'application/json'},
                async:true,
                context:$(this).find('.igoodsite'),
                error: function(){
                    $(this).text('');
                },
                success: function(data){
                    if(data.error!=''){
                        console.log(data);
                    }
                    $(this).text(data.value);
                }
            });

            $.ajax({
                url:'{~URL:"mode=parser;do=counthost"~}?url='+siteUrl,
                type:"GET",
                dataType: 'json',
                 beforeSend: function(){
                    $(this).html('<img src="{~URL:"0"~}assets/images/checker.gif" />');
                },
                requestHeaders: {Accept: 'application/json'},
                async:true,
                context:$(this).find('.counthost'),
                error: function(){
                    $(this).text('');
                },
                success: function(data){
                    if(data.error!=''){
                        console.log(data);
                    }
                    $(this).text(data.value);
                }
            });

            $.ajax({
                url:'{~URL:"mode=parser;do=inindex"~}?url='+siteUrl,
                type:"GET",
                dataType: 'json',
                 beforeSend: function(){
                    $(this).html('<img src="{~URL:"0"~}assets/images/checker.gif" />');
                },
                requestHeaders: {Accept: 'application/json'},
                async:true,
                context:$(this).find('.inindex'),
                error: function(){
                    $(this).text('');
                },
                success: function(data){
                    if(data.error!=''){
                        console.log(data);
                    }
                    $(this).text(data.value);
                }
            });

            $.ajax({
                url:'{~URL:"mode=parser;do=geo"~}?url='+siteUrl,
                type:"GET",
                dataType: 'json',
                 beforeSend: function(){
                    $(this).html('<img src="{~URL:"0"~}assets/images/checker.gif" />');
                },
                requestHeaders: {Accept: 'application/json'},
                async:true,
                context:$(this).find('.geosite'),
                error: function(){
                    $(this).text('');
                },
                success: function(data){
                    if(data.error!=''){
                        console.log(data);
                    }
                    $(this).text(data.value);
                }
            });
        });
    });
</script>
<?if(count($content)>0):?>
<table class="striped sitelist">
<thead><tr>
		<th>&nbsp;</th>
		<th>Возраст домена</th>
		<th>iGood</th>
        <th>Страниц в индексе</th>
        <th>Посещалка</th>
        <th>Регион</th>
	</tr></thead>
	<tbody>
    <?foreach($content as $item):?>
    <tr>
        <th><sub style="font-size:9px">Код ответа: <i class="codesite">???</i></sub>
            <br />
            <span class="urlsite" id="<?=$item;?>"><?=$item;?></span>
        </th>
		<td class="agesite">&nbsp;</td>
		<td class="igoodsite">&nbsp;</td>
        <td class="inindex">&nbsp;</td>
		<td class="counthost">&nbsp;</td>
        <td class="geosite">&nbsp;</td>
    </tr>
    <?endforeach;?>
    </tbody>
</table>
<?else:
header("Location: /");
endif;?>