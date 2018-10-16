<?php

include __DIR__ . "/../Data.php";

class DataTest extends PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function testBuildArrayParamsRangeCase()
    {
        /** @var Data $dataClassMock */
        $dataClassMock = $this->getMockBuilder('Data')->disableOriginalConstructor()
            ->setMethods(null)->getMock();

        $rangeSampleString = ['field1@range:1..7'];

        $dataClassMock->buildArrayParams($rangeSampleString);

        $this->assertCount(1, $dataClassMock->extraParams);
        $this->assertArrayHasKey('field1', $dataClassMock->extraParams);
        $this->assertCount(7, $dataClassMock->extraParams['field1']);
        $this->assertEquals(1, $dataClassMock->extraParams['field1'][0]);
        $this->assertEquals(7, $dataClassMock->extraParams['field1'][6]);
    }

    /**
     * @test
     */
    public function testBuildArrayParamsOptionsCase()
    {
        /** @var Data $dataClassMock */
        $dataClassMock = $this->getMockBuilder('Data')->disableOriginalConstructor()
            ->setMethods(null)->getMock();

        $optionsSampleString = ['field1@options:opA,opB,opC'];

        $dataClassMock->buildArrayParams($optionsSampleString);

        $this->assertCount(1, $dataClassMock->extraParams);
        $this->assertArrayHasKey('field1', $dataClassMock->extraParams);
        $this->assertCount(3, $dataClassMock->extraParams['field1']);
        $this->assertEquals('opA', $dataClassMock->extraParams['field1'][0]);
        $this->assertEquals('opB', $dataClassMock->extraParams['field1'][1]);
        $this->assertEquals('opC', $dataClassMock->extraParams['field1'][2]);
    }

    /**
     * @test
     */
    public function testBuildArrayParamsFixedFieldCase()
    {
        /** @var Data $dataClassMock */
        $dataClassMock = $this->getMockBuilder('Data')->disableOriginalConstructor()
            ->setMethods(null)->getMock();

        $sampleString = ['field1:value'];

        $dataClassMock->buildArrayParams($sampleString);

        $this->assertCount(1, $dataClassMock->extraParams);
        $this->assertArrayHasKey('field1', $dataClassMock->extraParams);
        $this->assertCount(1, $dataClassMock->extraParams['field1']);
        $this->assertEquals('value', $dataClassMock->extraParams['field1'][0]);
    }

    /**
     * @test
     */
    public function testSetPivotalParam()
    {
        /** @var Data $dataClassMock */
        $dataClassMock = $this->getMockBuilder('Data')->disableOriginalConstructor()
            ->setMethods(null)->getMock();

        $dataClassMock->extraParams = array(
            'A' => [1,2,3,4],
            'B' => ['A', 'B', 'C', 'D', 'F'],
            'C' => [23]
        );

        $dataClassMock->setPivotalParam();

        $this->assertEquals('B', $dataClassMock->pivotalParam);
    }
}