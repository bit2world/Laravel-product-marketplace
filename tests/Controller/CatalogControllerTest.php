<?php

class CatalogControllerTest extends AimeosTestAbstract
{
	public function testCountAction()
	{
		View::addLocation( dirname( __DIR__ ) . '/fixtures/views' );

		$response = $this->action( 'GET', '\Aimeos\Shop\Controller\CatalogController@countAction', ['site' => 'unittest'] );

		$this->assertResponseOk();
		$this->assertStringStartsWith( '{"', $response->getContent() );
	}


	public function testDetailAction()
	{
		View::addLocation( dirname( __DIR__ ) . '/fixtures/views' );

		$response = $this->action( 'GET', '\Aimeos\Shop\Controller\CatalogController@detailAction', ['site' => 'unittest', 'd_name' => 'Cafe_Noire_Cappuccino'] );

		$this->assertResponseOk();
		$this->assertStringContainsString( '<div class="section aimeos catalog-stage', $response->getContent() );
		$this->assertStringContainsString( '<div class="aimeos catalog-detail', $response->getContent() );
	}


	public function testListAction()
	{
		View::addLocation( dirname( __DIR__ ) . '/fixtures/views' );

		$response = $this->action( 'GET', '\Aimeos\Shop\Controller\CatalogController@listAction', ['site' => 'unittest'] );

		$this->assertResponseOk();
		$this->assertStringContainsString( '<div class="section aimeos catalog-filter', $response->getContent() );
		$this->assertStringContainsString( '<div class="section aimeos catalog-list', $response->getContent() );
	}


	public function testSessionAction()
	{
		View::addLocation( dirname( __DIR__ ) . '/fixtures/views' );

		$response = $this->action( 'GET', '\Aimeos\Shop\Controller\CatalogController@sessionAction', ['site' => 'unittest'] );

		$this->assertResponseOk();
		$this->assertStringContainsString( '<div class="section aimeos catalog-session', $response->getContent() );
		$this->assertStringContainsString( '<div class="section catalog-session-pinned', $response->getContent() );
		$this->assertStringContainsString( '<div class="section catalog-session-seen', $response->getContent() );
	}


	public function testStockAction()
	{
		View::addLocation( dirname( __DIR__ ) . '/fixtures/views' );

		$response = $this->action( 'GET', '\Aimeos\Shop\Controller\CatalogController@stockAction', ['site' => 'unittest'] );

		$this->assertResponseOk();
		$this->assertStringContainsString( '.aimeos .product .stock', $response->getContent() );
	}


	public function testSuggestAction()
	{
		View::addLocation( dirname( __DIR__ ) . '/fixtures/views' );

		$response = $this->action( 'GET', '\Aimeos\Shop\Controller\CatalogController@suggestAction', ['site' => 'unittest'], ['f_search' => 'Cafe'] );

		$this->assertResponseOk();
		$this->assertMatchesRegularExpression( '/[{.*}]/', $response->getContent() );
	}


	public function testTreeAction()
	{
		$root = \Aimeos\Controller\Frontend::create( app( 'aimeos.context' )->get(), 'catalog' )->getTree( \Aimeos\Controller\Frontend\Catalog\Iface::TREE );

		View::addLocation( dirname( __DIR__ ) . '/fixtures/views' );

		$response = $this->action( 'GET', '\Aimeos\Shop\Controller\CatalogController@treeAction', ['site' => 'unittest', 'f_catid' => $root->getId(), 'f_name' => 'test'] );

		$this->assertResponseOk();
		$this->assertStringContainsString( '<div class="section aimeos catalog-filter', $response->getContent() );
		$this->assertStringContainsString( '<div class="section aimeos catalog-list', $response->getContent() );
	}
}