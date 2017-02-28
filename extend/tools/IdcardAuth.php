<?php
namespace tools;

/**
* 实名认证类
*/
class IdcardAuth
{
	/* apikey */
	protected $key = null;
	/* 身份证号码 */
	protected $cardNo = null;
	/* 真实姓名 */
	protected $realName = null;
	/* 响应信息 */
	protected $reason = null;
	/* 返回数据 */
	protected $data = null;
	
	public function __construct($key , $cardNo , $realName) {
		$this->key = $key;
		$this->cardNo = $cardNo;
		$this->realName = $realName;
	}

	/**
	 * 获取错误信息
	 * @return [type] [description]
	 */
	public function getReason() {
		return $this->reason;
	}

	/**
	 * 调用认证接口
	 * @return [type] [description]
	 */
	public function auth() {
		if (empty($this->key) || empty($this->cardNo) || empty($this->realName)){
			$this->reason = '三个参数不能为空';
			return false;
		}
		
		// 这里验证一次身份证号格式
		if (!$this->checkIdcard($this->cardNo)){
			$this->reason = '身份证号码似乎有问题';
			return false;
		}

		$param = [ 'key' => $this->key , 'cardNo' => $this->cardNo , 'realName' => $this->realName ];

		// $res = $this->curl('http://apis.haoservice.com/idcard/VerifyIdcardReturnPhoto',$param);
		$res = $this->testData();
		if(empty($res)) {
			$this->reason = '网络接口访问失败';
			return false;
		}

		$temp = json_decode($res);

		if ($temp->error_code){
			$this->reason = $temp->reason;
			return false;
		}

		$this->data = [
			'realname'	=>	$temp->result->realname ,
			'idcard'	=>	$temp->result->idcard ,
			'isok'	=>	$temp->result->isok ,
			'idcardphoto'	=>	$temp->result->idCardPhoto ,
			'area'	=>	$temp->result->IdCardInfor->area ,
			'sex'	=>	$temp->result->IdCardInfor->sex ,
			'birthday'	=>	$temp->result->IdCardInfor->birthday ,
		];

		return true;
	}

	protected function testData() {
		return "{
			    \"error_code\": 0,
			    \"reason\": \"成功\",
			    \"result\": {
			        \"realname\": \"张强\",
			        \"idcard\": \"130321198804010180\",
			        \"isok\": true,
			        \"idCardPhoto\": \"\\/9j\\/4AAQSkZJRgABAgAAAQABAAD\\/\\/gAKSFMwMQVmAABcBwDPqwD\\/2wBDABgQEhUSDxgVExUbGRgcIzsmIyEhI0g0Nys7VktaWVRLU1FfaohzX2WBZlFTdqF4gY2RmJqYXHKns6aUsYiVmJL\\/2wBDARkbGyMfI0YmJkaSYlNikpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpL\\/wAARCADcALIDASIAAhEBAxEB\\/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL\\/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6\\/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL\\/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6\\/9oADAMBAAIRAxEAPwDodQ\\/1C\\/739DU8eBGuOmBVbUWO1F7Ek\\/5\\/OrdACOodCrdCKoCTFtJC\\/DKePzrQqjfx4kDgcMMH60AW7cYgQf7INV7tWSRZkHTrViH\\/AFMf+6P5U51DoVboRQBVhZZb1nHTbkfpT7yMkCVPvJUNmNlyykjIBH60681C2tFInkGf7g5NADrFizSsepIP86tVyaeIXiLCGBdp7sc1FPr95Iu3cIv9zigDfuZYbe7UvIACQT\\/Wr0c0Uo\\/dyI\\/+62a4GW6eQjt+NLHO56NigD0Gq98R5Sjvurk7fWbu3Xakq7fQruq6dX+0KHxtdc5Bfg0AbBabaICD9Mc1LBmCcxMeD0NTQSrNEkifdcZqO8Q4Ei9V60AWKKbG4dAw706gCvKM3kf0\\/wAasVXb\\/j9Tr0\\/xqxQBWuhsdZV4OcGrCsHUMOhpJEDoVPeq0U5iGxlzg+vSgC3RUf2iL+9+hooAouJPMjjnJAz3PQH3rSqgbaeQkv1A4yevtVi0lEkQHdRg0AT1Be\\/8e54zyPwqeobz\\/j3bnHI\\/GgBLOTfFtPVePw7VKzBVJY4A7ms\\/eLR1kb5Y2QEn+dc7resPfOYomZIF7f3vrQA\\/UNZbzGFo20A\\/frJeZ5AzOxZv9qougo+lMAJ4pysT81NHSl+4aQD\\/AOIU0vg0nJahio7UwH7W+8zUokKyA5pobdTcDI5pAdFoeqGPbDI42Fu4ztroVuba4XakqNuHQGvP2ZVX5Wp8Vwyt8rsv+7QB3lqSjvE3XqKs1yFpqEsOxkbzAOqmuqt7iO5hWSM\\/K1AEZP8Apw9v8Ks1WOBesScADP6VJHcI7beR6ZoAlqhtZlMpwRnmrzHCk5xgVDaAGFgehP8ASgCnRVs2gJOHwPTFFAFiqcn+j3Yf+B+v9f8AGrlR3EQljIx8w5FAElQ3g\\/0dvbH86hju9iqjoflGCR\\/hS6lKI7GSQEEBc\\/XvQBzviC\\/Mu21XGyNRn3asEnjmpZCXOTyScmo2HzUAM9KHGORS\\/wAdLQAGlYZpE+9in7d1AEYOKbt5qTb81Io+YigBvQ0tPK5pVSgCJhihQSac33qOhoAkRyDiuk8M3p3vBKevzVzAbJxWjo8uy+iBOFb5c4oA6p2DPI3ODwD+NTGAG3Uj7wGeO9MlRY4V2nO45z\\/9argAAAHQUAQJJvtmz1CkGiz\\/ANUf96o5AYXbH3HBqSz\\/ANUf96gCeiiigApGYKpY9AM1HHcxyMVBwe2e9Om\\/1L\\/7poAzkUAB3BKEkHFQ3shTTbiMk\\/dJH0x\\/+qtK3jWS1CvyDn8KzdXgeHTpnJDALge1AHKMeRTNvzUpOQTUiLv20FECp+8p2zc1XPIxOysKZLE3msv91qm47FZ0xzT1hyOasvBmQqvap7a3ZsqRwKVxpFLytvzbaWCEsx4rUe03bV21JbwLl6LlWKEVozu3y\\/LU32AqRkcGta1hSL7y7qlmjVoSe45oA5qSzJlZVFV5rdkHzV00MSF2yOcVV1C13KzbaLktHNYwatWj\\/v4h\\/Dmo7iPa2KSD\\/WLVkHcNu4jOPlJArQqlOylkkU53DdVxWDKGHQ0CI7hN8RPdeabaf6sj3qWT\\/Vtn0NVbZ9smD0bigC5RRRQBTa3At1kTIcDceaf5peyZiecYNWQMDFUnX7O7p\\/A6nH9KALFp\\/wAe6fj\\/ADqvrC7tKuF\\/2KsWn\\/Hun4\\/zp7qHQqf4higDzbo2KvWCbpV2\\/wB6ob+LyrlxtZfm6NVvSo3kYkZwPeky0aLwbrolfSnPZb5mbb96rFrnzGJ6kVZ+WsjRFRLJFZmamzRiNwyLweoq7UU4\\/dNTGVArkGTJp9sowxpUfbGyEdRxikhyjjdwGHegRaWpVbbTPu\\/eqPzkX+KqQhImCTlexOKfOm9GqqZFkd9jdDmratvQH86AOW1FNshqC3G6ZF6fNWvq8H7xTiqVnHuvI1XqW7VSIZ17oTLHGW3YUAmpbYlHaNuvai3RjI0jgg9siluVIxKnBHWmQSy\\/6p\\/901VCfuN44INTtIHt2YemDRAF+z89DnNADRdLgZBz3xRVY4BODketFAGhVe9XMQbHKn9KnRg6hh0NNuFDQOD6ZoAbaf8AHun4\\/wA6lqpYvgshPXkVboA5DxFbLFflhj5\\/m4\\/hp+kHy4w5Hyt1q54sjPkxSL0BxTNGQNYJkA89xUTNIA0siN5i9zz70sjEr8rc1YmiDx7QAPSo7YJtKt1WkUQRLL97e1TSyMIm3U51\\/u1XkfdGRSuVYtREeSvFJcMrp7jkVHCjGEU11ZaQERbzU5bkdahe1Zm3bmpyDypct901YW9tMYM0YPuaq5NiraZSZg2avwt5UpUn5T61FEY3d2Qhl\\/vCpiocbeh9aYEOqJuQnP3RVHRITLeAjt0rTuIdtpIScllqv4Z2I8jucHG0VSJZ0NtJvTBOSKlqq58ucOvQ9f61apmZWNs2SFbCn1NSy\\/JAQOwxUlQ3X+rH1oAiW3dlBBXkZoq0gIRQeoFFAFezYqWifgjoDU83+pf\\/AHTVe7QqyzJwRwcVJJKGtWdccjBHpQBVCmOOOYeuOv8An3qe9n8m1Mickj5aWJA1mFbgEHr2qpKfMsZYv4lG4UMa3MnVke4so5XVVb\\/Zq1pC4sIx7UkwVtNQN\\/dqexH+iJj0NYm7XvDpVbb8v3qzpbeQSlnkfaRyI\\/lxWttpk8e+Mjv2oAxI7G7M5aa4fYPu\\/N96rKQusGHbc2fvVZhIZMHtUsg\\/dE0w2JLdV8haV0VqasiqqBjgkCn9elCAy7+38wxoDgA7iP71UH08PPv8zCj+H0ralXdcZHb\\/AAqKSHcxOBzTFuQw2SszMnyDP8FXooti\\/M1Ns+MoeDmrVIZFOP3DCqWnxFI2SP5Tj722r8\\/+qaqlrNtl8or3oBE2ku88UscjMzq2RmtS3fdHg9V4qlo8eyORz\\/G9Wn\\/cz7v4W61otjKp8RYqG6+4B71NVe7\\/AIPxpkFiiiigAIBGDyKqGzbcQrjZnvVuigCC6wlvtA4OB9KrywlI0dehHJ96sXv+qH+9UgQNCEPTbigDDvEAtQF6Lx+NWLYhLVCeBszTrqEhtmcAHIqOckQ\\/U1m1qbxldEySK5wpzTmWq5i2Isi9VxkVMsm+IN+dIZVdRBLvH3T1p1xIHjUKepzUd3cKAUK5qKJo4oBNI42g9KQE6R73YE\\/dGM1JFlco3bpVG01FZlkZc4HqKjm1ONJ1+Y5FUhFt5tl4A3f\\/AAq38rfNWXdyrKRIvUmrVpLuRaQyaRfKkVx+NSlsjI6GkfDqQarCRkUr3BpiLErfujUMEP73zdvptpHZyNrjnOasJNBaR+fctsCgLkjNMXNY0oYlhiVF6CidN8ZA6jkVFb3ttdcQTo\\/sG5qxVmJVErlVReo70hLM6JIOhxSsRHcZA4B6USPvk3IPujNAFqioRcLgZBzRQAtrIHiA7rwalqr\\/AMe9z6I9WqAK18eEH1qzVa9GTGPr\\/SrNAFW9j3JvHUcGqFyhCRnj58\\/dq3qGpWtmjCVwXIxsX71Y1pfG9DAgLsb5QKTKiax5HNVT+6lKfwt0q1Ve5Tcu4dR\\/KsjYqhDNKWxTJrYE7DnYatwBdpPqaWXa0ZLduaBXK9tAFhIxxmopNKiY5BYf7tWILhApGe9OW9i3bf4aY9StBbBZAjEn61aSJYvlWoZpwJi6NxVhZFYZFMVx6tUU+A4I696fTWG6dR7UhEV3MBbswHIDNXO3mq3NzCIpSAnt\\/FW5rDJDp8hA+Y\\/LXLhsnmrRDGq7VoWms31oRsuGZR\\/A\\/Iqi8XdajqiDqrPxLFJKVvItgYY3JyPyrftZradC9s6SA+jZrzbNWLe5mt33wyuh9Q22gD0H7O3YiiuR\\/wCEl1H\\/AJ6p\\/wB8LRQB2Vym+InuvIqD7RIyoife6E+tWndIkLuwVR1Jrl7\\/AF0JdMLFcKvVyP5UAal1fxwbDctgqenc1k6h4jmlylp+6T+9\\/E3\\/AMTWLPO80jSO5Zj95jURagB7uztuZqt6PKsV6ATwwx+NZ9JvKurJwRyKGNHdhsgEdDTZW+RvpVTS7tbm3DL26r6Gp5\\/uN9KxNzPZpS+2M7c\\/xVK0EvlESv8AlT1UGAZIB7UySdvLCUCIPs6xhW3Ng9eam+w27fxHP+9UUgeRQoXtRC0o+UjkUXKuNlsH8wok5A\\/2hUtlujyjHOKehLTnNEnyShuxqiS0rbqjLf6QPpSq1Qu+Jv8APpSEZ\\/iOfKJCvrvrCFbGvo3mwt\\/stWOv3qtGctyRWpXi3fd+9TF4NSLVElenLUjIHz61D0ODQBJRTd1FUBp3OoXF9cb5n3Kvzbf4V\\/3azydspqVflTd\\/eqGbqakCUsvWog2eabu3LtpoznigCXdTXo3VNaRCSQl\\/uLzQBp6IkkO9jxnBC1sSyboiQetZ+k5luJ89Cq1ckgkK4Ud6ze5sthUQuyoeOP8A69OjUK5STv0NRRSbZ2B4pZ5VYZB5FSMtNtX7tQyJuO9OtQG53KDuprXdMVy1GuCxbqarMm5mwxOP71J9q\\/2qgjuc5x3oAninC\\/KadAGknZjUCxsW8yr1uu1CT1JpgZviDHlRHPKvWE67G21s6780Q\\/3qzbiAmDeP4atEMrb8VKkoNVadTILLnuKR03Ju7imKcpUyHNAFXdRU\\/kiigBXbdFu\\/2qgJzT3b5dq\\/dpFHFAEe2ngYWnKtI1ADTWlaqFs09WbNZlbJTyooVPGAOtMC1oTfvbhf4t1bdc3psvk6g277rV0atWMtzaJVMIeR\\/rVaWF1+61Xov9ZJ9aHRWpAYLI6vhjwaayt\\/erUuoNy7vSq4t93NO4FRUYnNW7O3yD9amW3VVP0qe2X5PxoAesYwQe9LESMoeop9V7txEu8fSgDI1eTe6xrVhYQbYg9CtVLeMz3DSGthFxHitoGUzkDwaKnvIvKuXSoKYgU4qVXqCpU+7UgS76KbRVANApcUfSg0gAnFNalptAElsnm3CJ\\/eat6\\/GefSsjShuvkz\\/Dlqs3eqs0p2ouygCOQsHV63dPuvNiX+9WQU3Rj+667lpLK4aCXGOKzlE0R0Fs2S5qVqr2Z3qzVaqCiJqrKBG5B6dqubahnjG0HvQBEJCQQV4Ip8I+SlVQYOeop8IBXb3FUA1qoXpadii1oTrtWqqRgHP8VOMbilIitI1jQL71b\\/AIK5zUp3Fy8auQBirWkzSLGfMYkH1raJkU9XH+mfVapfw1f1f\\/j7H+5VKpYDKelMp6UgHUUUVQDiMdKaetPpg60gCmGpKa1MCxpbbbkt\\/sNUD\\/Ntp1s5WcY7jFXoIkaZMj+9\\/wChUATwsLi1Rdux41UUsixofm4zSSfKhZeDTrlQywOeppAaGmSKcoD1rUVa5jcYyGQ4NbWnXMsow7ZxUONjWLuXdtMuF+QfWnO7CVVB4OKlqBkC244JPGO1VriaKBt4ZcCrNy7JAzr1Fcdc3EkrtvbNVFCbsbK6lC7szyrtX7q1Qu9WYlkgGE9aroi7elJJEuzOK0MisS8kmXJJPc1qQqERFziqFsoMtXb3\\/UmqQFPUZRLc5X7qjFV6VaWkBFt+anjpSClH3aAFooooA\\/\\/Z\",
			        \"IdCardInfor\": {
			            \"area\": \"山西省太原市清徐县\",
			            \"sex\": \"男\",
			            \"birthday\": \"1985-4-10\"
			        }
			    }
			}";
	}

	public function getData() {
		return $this->data;
	}

	/**
	 * 身份证号第一次校验
	 * @param  [type] $idcard [description]
	 * @return [type]         [description]
	 */
	protected function checkIdcard($idcard){  
  
	    if(strlen($idcard)!=18){  
	        return false;  
	    }

	    $idcard_base = substr($idcard, 0, 17);  
	    $verify_code = substr($idcard, 17, 1);  
	    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);  
	    $verify_code_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');  
	  
	    $total = 0;  
	    for($i=0; $i<17; $i++){  
	        $total += substr($idcard_base, $i, 1)*$factor[$i];  
	    }  
	  
	    $mod = $total % 11;  
	  
	    if($verify_code == $verify_code_list[$mod]){  
	        return true;  
	    }else{  
	        return false;  
	    }
	}

	/**
	 * 网络访问
	 * @param  string $destURL  [description]
	 * @param  array  $paramArr [description]
	 * @param  string $flag     [description]
	 * @param  string $fromurl  [description]
	 * @return [type]           [description]
	 */
	protected function curl($destURL = '', $paramArr = array()){
	    $curl = curl_init();
	    $paramStr = '';
	  	if( !empty($paramArr) ){
	    	foreach ($paramArr as $key => $value) {
	    		$paramStr .= $key.'='.$value.'&'; 
	    	}
	    }
	    $destURL = $paramArr ? $destURL."?".$paramStr : $destURL;
	    // 请求地址
	    curl_setopt($curl, CURLOPT_URL, $destURL);
	    curl_setopt($curl, CURLOPT_TIMEOUT, 10);		
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);  
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
	    $str = curl_exec($curl);  
	    curl_close($curl);

	   	return $str;  
	}
	
}