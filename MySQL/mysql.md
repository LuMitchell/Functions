## 原生写法（mysqli）
```
$link = mysqli_connect('192.168.1.xxx', $db_user, $db_passwd, 'table');

$sql = 'select count(1) as num from user';
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($result);//数组

mysqli_free_result($result);
mysqli_close($link);
```
