<?
class db 
{
	private $dbc = ['localhost','root','','bote'];
	private $dbo ;
	function __construct()
	{
		$this->dbo = new mysqli($this->dbc[0],$this->dbc[1],$this->dbc[2],$this->dbc[3]);
		if (!($this->dbo instanceof mysqli) || $this->dbo->connect_errno)
			throw new Exception("Error db connect: ".$this->dbo->connect_error , 1);
		$this->dbo->set_charset('utf8');
	}
	public function q($sql = '')
	{
		if (empty($sql)) return false;
		$res = $this->dbo->query($sql);
		$this->err();
		if (is_bool($res)){
			return $res;
		}elseif ($res instanceof mysqli_result) {
			$arr = [];
			while($arr[] = $res->fetch_assoc()){}
			$res->close();
			return $arr;
		}else{
			return $res;
		}
	}
	public function getTabl()
	{
		$arr = $this->q('show tables');
		$tabl = [];
		for ($i=0; $i < count($arr) ; $i++) { 
			if (!is_array($arr[$i])) continue;
			$tabl[] = $arr[$i][array_keys($arr[$i])[0]];
		}
		return $tabl;
	}
	public function is_table($tabl = '')
	{
		return in_array(trim($tabl), $this->getTabl());
	}
	private function err(){
		if ($this->dbo->errno){
			throw new Exception($this->dbo->error, $this->dbo->errno);
		}
	}
}
