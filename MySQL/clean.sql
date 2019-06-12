#清洗数据
#将2000w的数据表里的重复数据清除，先copy表结构，再建立unique索引，最后insert ignore
#（期间sql mode 可能需要更改，去掉`ONLY_FULL_GROUP_BY`）
#执行500w需要6分钟左右（2CPU，4G）

INSERT IGNORE INTO xx_copy (SELECT * FROM xx where id >= 5000000 and id < 10000000);
