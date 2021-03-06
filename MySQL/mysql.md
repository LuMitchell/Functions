## 原生写法（mysqli）
```
$link = mysqli_connect('192.168.1.xxx', $db_user, $db_passwd, 'table');

$sql = 'select count(1) as num from user';
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($result);//数组

mysqli_free_result($result);
mysqli_close($link);
```


## 清洗数据

### 去重

> 将2000w的数据表里的重复数据清除，先copy表结构，再建立unique索引，最后insert ignore
> （期间sql mode 可能需要更改，去掉`ONLY_FULL_GROUP_BY`）
> 执行500w需要6分钟左右（2CPU，4G）

```
INSERT IGNORE INTO xx_copy (SELECT * FROM xx where id >= 5000000 and id < 10000000);
```

### 两表字段更新

> 将B表的某个字段的值更新到对应的A表里面的某个字段中

```
UPDATE hospital_doctor AS h, hospital_doctor_extra AS ex SET h.`summary` = ex.`summary` WHERE h5.id = ex.id AND ex.summary IS NOT null;
```
