<?php
/**
 * author: selfimpr
 * mail: lgg860911@yahoo.com.cn
 * blog: http://blog.csdn.net/lgg201
 * �����ᵽ�Ĵ�����PHP5.3���ϰ汾����ͨ��.
 */
 
function callback($callback) {
    $callback();
}
 
//���: This is a anonymous function.<br />\n
//������ֱ�Ӷ���һ�������������д���, �������İ汾��, ���ǲ����õ�.
//����, �����﷨�ǳ����, ��javascript�﷨����һ��, ֮����˵������, ��Ҫ�������¿�
//����: һ��������﷨��Ȼ���ܻ�ӭ��.
callback(function() {
    print "This is a anonymous function.<br />\n";
});
 
//���: This is a closure use string value, msg is: Hello, everyone.<br />\n
//�������ȶ�����һ���հ�, ��λ��ڱ�����������...
//use, һ�����ʵļһ�...
//������֪, �հ�: �ڲ�����ʹ�����ⲿ�����ж���ı���.
//��PHP�¿��ŵıհ��﷨��, ���Ǿ�����use��ʹ�ñհ��ⲿ����ı�����.
//��������ʹ�����ⲿ����$msg, ������֮��, �ֶ���ֵ�����˸ı�, �հ���ִ�к��������ԭʼֵ
//����: �Դ�ֵ��ʽ���ݵĻ������Ͳ���, �հ�use��ֵ�ڱհ������Ǿ�ȷ����.
$msg = "Hello, everyone";
$callback = function () use ($msg) {
    print "This is a closure use string value, msg is: $msg. <br />\n";
};
$msg = "Hello, everybody";
callback($callback);
 
//���: This is a closure use string value lazy bind, msg is: Hello, everybody.<br />\n
//��һ�����÷�ʽ, ����ʹ�����õķ�ʽ��use
//���Է����������Ǳհ�������ֵ...
//�����ʵ�������, ���������÷�ʽuse, �Ǳհ�use����$msg��������ĵ�ַ
//�������$msg�����ַ�ϵ�ֵ�����˸ı�֮��, �հ�������������ַ��ֵʱ, ��Ȼ�ı���.
$msg = "Hello, everyone";
$callback = function () use (&$msg) {
    print "This is a closure use string value lazy bind, msg is: $msg. <br />\n";
};
$msg = "Hello, everybody";
callback($callback);
 
//���: This is a closure use object, msg is: Hello, everyone.<br />\n
//�հ����������֮ǰ��������ֵΪHello, everyone�Ķ���, �����Ƕ�$obj������ֵ�һ�����¸�ֵ.
//������������
//1. obj�Ƕ���Hello, everyone������
//2. ����Hello, everyone���հ�use, �հ�������һ����Hello, everyone���������
//3. obj���޸�ΪHello, everybody������������
//4. ע��, ������obj�����ʵ�����, ������Hello, everyone����, ����Ȼ�հ����������ǰ���Hello, everyone
$obj = (object) "Hello, everyone";
$callback = function () use ($obj) {
    print "This is a closure use object, msg is: {$obj->scalar}. <br />\n";
};
$obj = (object) "Hello, everybody";
callback($callback);
 
//���: This is a closure use object, msg is: Hello, everybody.<br />\n
//���ǰ�������Ĳ���, �����Ͱ������:
//1. obj����ָ��Hello, everyone����
//2. �հ�����һ������ָ��Hello, everyone����
//3. �޸�obj����ָ��Ķ���(��Hello, everyone����)��scalarֵ
//4. ִ�бհ�, �������Ȼ��Hello, everybody, ��Ϊ��ʵֻ��һ�������Ķ���
$obj = (object) "Hello, everyone";
$callback = function () use ($obj) {
    print "This is a closure use object, msg is: {$obj->scalar}. <br />\n";
};
$obj->scalar = "Hello, everybody";
callback($callback);
 
//���: This is a closure use object lazy bind, msg is: Hello, everybody.<br />\n
//�հ����õ���ʲô��? &$obj, �հ�����������ָ��$obj���������ָ��ĵ�ַ.
//���, ����obj��ô�仯, �����Ӳ��ѵ�....
//����, ����ľ��Ǹı���ֵ
$obj = (object) "Hello, everyone";
$callback = function () use (&$obj) {
    print "This is a closure use object lazy bind, msg is: {$obj->scalar}. <br />\n";
};
$obj = (object) "Hello, everybody";
callback($callback);
 
/**
 * һ�����ñհ��ļ�����������
 * ������ʵ�������python�н��ܱհ�ʱ������...
 * ���ǿ�����������:
 *         1. counter����ÿ�ε���, ����һ���ֲ�����$counter, ��ʼ��Ϊ1.
 *         2. Ȼ�󴴽�һ���հ�, �հ������˶Ծֲ�����$counter������.
 *         3. ����counter���ش����ıհ�, �����پֲ�����, ����ʱ�бհ���$counter������, 
 *             �������ᱻ����, ���, ���ǿ����������, ������counter���صıհ�, Я����һ������̬��
 *             ����.
 *         4. ����ÿ�ε���counter���ᴴ��������$counter�ͱհ�, ��˷��صıհ��໥֮���Ƕ�����.
 *         5. ִ�б����صıհ�, ����Я��������̬��������������, �õ��ľ���һ��������.
 * ����: �˺����������������໥�����ļ�����.
 */
function counter() {
    $counter = 1;
    return function() use(&$counter) {return $counter ++;};
}
$counter1 = counter();
$counter2 = counter();
echo "counter1: " . $counter1() . "<br />\n";
echo "counter1: " . $counter1() . "<br />\n";
echo "counter1: " . $counter1() . "<br />\n";
echo "counter1: " . $counter1() . "<br />\n";
echo "counter2: " . $counter2() . "<br />\n";
echo "counter2: " . $counter2() . "<br />\n";
echo "counter2: " . $counter2() . "<br />\n";
echo "counter2: " . $counter2() . "<br />\n";
?>