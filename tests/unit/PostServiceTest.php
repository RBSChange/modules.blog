<?php
/**
 * @author intportg
 * @package modules.blog
 */
class modules_blog_tests_PostServiceTest extends blog_tests_AbstractBaseUnitTest
{
	public function testCountersUpdate()
  	{
  		// -- Intinialization.
  		
  		$wtf = website_TestFactory::getInstance();
  		$btf = blog_TestFactory::getInstance();
  		$ts = blog_KeywordService::getInstance();
  		
  		$website = $wtf->getNewWebsite();
  		$topic = $wtf->getNewTopic($website);
  		$blog = $btf->getNewBlog($topic);
  		$blog2 = $btf->getNewBlog($topic);
  		
  		$categoryFolder = blog_CategoryfolderService::getInstance()->getByBlog($blog);
  		$this->assertNotNull($categoryFolder);
  		$category1 = $btf->getNewCategory($categoryFolder);
  		$category11 = $btf->getNewCategory($category1);
  		$category12 = $btf->getNewCategory($category1);
  		$category121 = $btf->getNewCategory($category12);
  		$category2 = $btf->getNewCategory($categoryFolder);
  		$category21 = $btf->getNewCategory($category2);
  		
  		$keywordFolder = blog_KeywordfolderService::getInstance()->getByBlog($blog);
  		$this->assertNotNull($keywordFolder);
  		$keyword1 = $btf->getNewKeyword($keywordFolder, array('label' => 'keyword 1'));
  		$keyword2 = $btf->getNewKeyword($keywordFolder, array('label' => 'keyword 2'));
  		$keyword3 = $btf->getNewKeyword($keywordFolder, array('label' => 'keyword 3'));
  		
  		// -- Tests.
  		
  		$this->assertEquals(0, $category1->getPublishedPostCount());
  		$this->assertEquals(0, $category11->getPublishedPostCount());
  		$this->assertEquals(0, $category12->getPublishedPostCount());
  		$this->assertEquals(0, $category121->getPublishedPostCount());
  		$this->assertEquals(0, $category2->getPublishedPostCount());
  		$this->assertEquals(0, $category21->getPublishedPostCount());
  		
  		$this->assertEquals(0, $category1->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category11->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category12->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category121->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category2->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category21->getRecursivePublishedPostCount());
  		
  		$this->assertEquals(0, $keyword1->getPublishedPostCount());
  		$this->assertEquals(0, $keyword2->getPublishedPostCount());
  		$this->assertEquals(0, $keyword3->getPublishedPostCount());
  		
  		$this->assertEquals(0, $keyword1->getPostCount());
  		$this->assertEquals(0, $keyword2->getPostCount());
  		$this->assertEquals(0, $keyword3->getPostCount());
  		  		
  		// Creating posts increments keywords counters.
  		
  		$post1 = $btf->getNewPost($blog, array('category' => array($category21, $category121), 'keyword' => array($keyword1)));
  		$this->assertNotPublished($post1);
  		
  		$this->assertEquals(0, $category1->getPublishedPostCount());
  		$this->assertEquals(0, $category11->getPublishedPostCount());
  		$this->assertEquals(0, $category12->getPublishedPostCount());
  		$this->assertEquals(0, $category121->getPublishedPostCount());
  		$this->assertEquals(0, $category2->getPublishedPostCount());
  		$this->assertEquals(0, $category21->getPublishedPostCount());
  		
  		$this->assertEquals(0, $category1->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category11->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category12->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category121->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category2->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category21->getRecursivePublishedPostCount());
  		
  		$this->assertEquals(0, $keyword1->getPublishedPostCount());
  		$this->assertEquals(0, $keyword2->getPublishedPostCount());
  		$this->assertEquals(0, $keyword3->getPublishedPostCount());
  		
  		$this->assertEquals(1, $keyword1->getPostCount());
  		$this->assertEquals(0, $keyword2->getPostCount());
  		$this->assertEquals(0, $keyword3->getPostCount());
  		
  		$post2 = $btf->getNewPost($blog, array('category' => array($category11, $category121, $category2), 'keyword' => array($keyword1, $keyword2)));
  		$this->assertNotPublished($post2);
  		
  		$this->assertEquals(0, $category1->getPublishedPostCount());
  		$this->assertEquals(0, $category11->getPublishedPostCount());
  		$this->assertEquals(0, $category12->getPublishedPostCount());
  		$this->assertEquals(0, $category121->getPublishedPostCount());
  		$this->assertEquals(0, $category2->getPublishedPostCount());
  		$this->assertEquals(0, $category21->getPublishedPostCount());
  		
  		$this->assertEquals(0, $category1->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category11->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category12->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category121->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category2->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category21->getRecursivePublishedPostCount());
  		
  		$this->assertEquals(0, $keyword1->getPublishedPostCount());
  		$this->assertEquals(0, $keyword2->getPublishedPostCount());
  		$this->assertEquals(0, $keyword3->getPublishedPostCount());
  		
  		$this->assertEquals(2, $keyword1->getPostCount());
  		$this->assertEquals(1, $keyword2->getPostCount());
  		$this->assertEquals(0, $keyword3->getPostCount());
  		
  		// Adding or removing keyword correctly update keywords counters.
  		  		
  		$post1->addKeyword($keyword3);
  		$post1->save();
  		
  		$this->assertEquals(0, $category1->getPublishedPostCount());
  		$this->assertEquals(0, $category11->getPublishedPostCount());
  		$this->assertEquals(0, $category12->getPublishedPostCount());
  		$this->assertEquals(0, $category121->getPublishedPostCount());
  		$this->assertEquals(0, $category2->getPublishedPostCount());
  		$this->assertEquals(0, $category21->getPublishedPostCount());
  		
  		$this->assertEquals(0, $category1->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category11->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category12->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category121->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category2->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category21->getRecursivePublishedPostCount());
  		
  		$this->assertEquals(0, $keyword1->getPublishedPostCount());
  		$this->assertEquals(0, $keyword2->getPublishedPostCount());
  		$this->assertEquals(0, $keyword3->getPublishedPostCount());
  		
  		$this->assertEquals(2, $keyword1->getPostCount());
  		$this->assertEquals(1, $keyword2->getPostCount());
  		$this->assertEquals(1, $keyword3->getPostCount());
  		
  		$post1->removeKeyword($keyword3);
  		$post1->save();
  		
  		$this->assertEquals(0, $category1->getPublishedPostCount());
  		$this->assertEquals(0, $category11->getPublishedPostCount());
  		$this->assertEquals(0, $category12->getPublishedPostCount());
  		$this->assertEquals(0, $category121->getPublishedPostCount());
  		$this->assertEquals(0, $category2->getPublishedPostCount());
  		$this->assertEquals(0, $category21->getPublishedPostCount());
  		
  		$this->assertEquals(0, $category1->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category11->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category12->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category121->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category2->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category21->getRecursivePublishedPostCount());
  		
  		$this->assertEquals(0, $keyword1->getPublishedPostCount());
  		$this->assertEquals(0, $keyword2->getPublishedPostCount());
  		$this->assertEquals(0, $keyword3->getPublishedPostCount());
  		
  		$this->assertEquals(2, $keyword1->getPostCount());
  		$this->assertEquals(1, $keyword2->getPostCount());
  		$this->assertEquals(0, $keyword3->getPostCount());
  		
  		// Plublishing or depublishig posts correctly updates counters.
  		
  		$post1->activate();
  		$this->assertPublished($post1);
  		
  		$this->assertEquals(0, $category1->getPublishedPostCount());
  		$this->assertEquals(0, $category11->getPublishedPostCount());
  		$this->assertEquals(0, $category12->getPublishedPostCount());
  		$this->assertEquals(1, $category121->getPublishedPostCount());
  		$this->assertEquals(0, $category2->getPublishedPostCount());
  		$this->assertEquals(1, $category21->getPublishedPostCount());
  		
  		$this->assertEquals(1, $category1->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category11->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category12->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category121->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category2->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category21->getRecursivePublishedPostCount());
  		
  		$this->assertEquals(1, $keyword1->getPublishedPostCount());
  		$this->assertEquals(0, $keyword2->getPublishedPostCount());
  		$this->assertEquals(0, $keyword3->getPublishedPostCount());
  		
  		$this->assertEquals(2, $keyword1->getPostCount());
  		$this->assertEquals(1, $keyword2->getPostCount());
  		$this->assertEquals(0, $keyword3->getPostCount());
  		
  		$post2->activate();
  		$this->assertPublished($post2);
  		
  		$this->assertEquals(0, $category1->getPublishedPostCount());
  		$this->assertEquals(1, $category11->getPublishedPostCount());
  		$this->assertEquals(0, $category12->getPublishedPostCount());
  		$this->assertEquals(2, $category121->getPublishedPostCount());
  		$this->assertEquals(1, $category2->getPublishedPostCount());
  		$this->assertEquals(1, $category21->getPublishedPostCount());
  		
  		$this->assertEquals(2, $category1->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category11->getRecursivePublishedPostCount());
  		$this->assertEquals(2, $category12->getRecursivePublishedPostCount());
  		$this->assertEquals(2, $category121->getRecursivePublishedPostCount());
  		$this->assertEquals(2, $category2->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category21->getRecursivePublishedPostCount());
  		
  		$this->assertEquals(2, $keyword1->getPublishedPostCount());
  		$this->assertEquals(1, $keyword2->getPublishedPostCount());
  		$this->assertEquals(0, $keyword3->getPublishedPostCount());
  		
  		$this->assertEquals(2, $keyword1->getPostCount());
  		$this->assertEquals(1, $keyword2->getPostCount());
  		$this->assertEquals(0, $keyword3->getPostCount());
  		
  		$post1->deactivate();
  		
  		$this->assertEquals(0, $category1->getPublishedPostCount());
  		$this->assertEquals(1, $category11->getPublishedPostCount());
  		$this->assertEquals(0, $category12->getPublishedPostCount());
  		$this->assertEquals(1, $category121->getPublishedPostCount());
  		$this->assertEquals(1, $category2->getPublishedPostCount());
  		$this->assertEquals(0, $category21->getPublishedPostCount());
  		
  		$this->assertEquals(1, $category1->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category11->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category12->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category121->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category2->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category21->getRecursivePublishedPostCount());
  		
  		$this->assertEquals(1, $keyword1->getPublishedPostCount());
  		$this->assertEquals(1, $keyword2->getPublishedPostCount());
  		$this->assertEquals(0, $keyword3->getPublishedPostCount());
  		
  		$this->assertEquals(2, $keyword1->getPostCount());
  		$this->assertEquals(1, $keyword2->getPostCount());
  		$this->assertEquals(0, $keyword3->getPostCount());
  		
  		// Adding or removing keyword correctly update keywords counters.
  		  		
  		$post2->addKeyword($keyword3);
  		$post2->save();
  		
  		$this->assertEquals(0, $category1->getPublishedPostCount());
  		$this->assertEquals(1, $category11->getPublishedPostCount());
  		$this->assertEquals(0, $category12->getPublishedPostCount());
  		$this->assertEquals(1, $category121->getPublishedPostCount());
  		$this->assertEquals(1, $category2->getPublishedPostCount());
  		$this->assertEquals(0, $category21->getPublishedPostCount());
  		
  		$this->assertEquals(1, $category1->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category11->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category12->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category121->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category2->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category21->getRecursivePublishedPostCount());
  		
  		$this->assertEquals(1, $keyword1->getPublishedPostCount());
  		$this->assertEquals(1, $keyword2->getPublishedPostCount());
  		$this->assertEquals(1, $keyword3->getPublishedPostCount());
  		
  		$this->assertEquals(2, $keyword1->getPostCount());
  		$this->assertEquals(1, $keyword2->getPostCount());
  		$this->assertEquals(1, $keyword3->getPostCount());
  		
  		$post2->removeKeyword($keyword3);
  		$post2->save();
  		
  		$this->assertEquals(0, $category1->getPublishedPostCount());
  		$this->assertEquals(1, $category11->getPublishedPostCount());
  		$this->assertEquals(0, $category12->getPublishedPostCount());
  		$this->assertEquals(1, $category121->getPublishedPostCount());
  		$this->assertEquals(1, $category2->getPublishedPostCount());
  		$this->assertEquals(0, $category21->getPublishedPostCount());
  		
  		$this->assertEquals(1, $category1->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category11->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category12->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category121->getRecursivePublishedPostCount());
  		$this->assertEquals(1, $category2->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category21->getRecursivePublishedPostCount());
  		
  		$this->assertEquals(1, $keyword1->getPublishedPostCount());
  		$this->assertEquals(1, $keyword2->getPublishedPostCount());
  		$this->assertEquals(0, $keyword3->getPublishedPostCount());
  		
  		$this->assertEquals(2, $keyword1->getPostCount());
  		$this->assertEquals(1, $keyword2->getPostCount());
  		$this->assertEquals(0, $keyword3->getPostCount());
  		
  		// Deleting posts decrements counters.

  		$post2->delete();
  		
  		$this->assertEquals(0, $category1->getPublishedPostCount());
  		$this->assertEquals(0, $category11->getPublishedPostCount());
  		$this->assertEquals(0, $category12->getPublishedPostCount());
  		$this->assertEquals(0, $category121->getPublishedPostCount());
  		$this->assertEquals(0, $category2->getPublishedPostCount());
  		$this->assertEquals(0, $category21->getPublishedPostCount());
  		
  		$this->assertEquals(0, $category1->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category11->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category12->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category121->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category2->getRecursivePublishedPostCount());
  		$this->assertEquals(0, $category21->getRecursivePublishedPostCount());
  		
  		$this->assertEquals(0, $keyword1->getPublishedPostCount());
  		$this->assertEquals(0, $keyword2->getPublishedPostCount());
  		$this->assertEquals(0, $keyword3->getPublishedPostCount());
  		
  		$this->assertEquals(1, $keyword1->getPostCount());
  		$this->assertEquals(0, $keyword2->getPostCount());
  		$this->assertEquals(0, $keyword3->getPostCount());
  		
  		// Check keywords creation using property keywordsText.
  		
  		$this->assertNull($ts->getByLabelInBlog('Chuck Norris', $blog));
  		$this->assertNull($ts->getByLabelInBlog('Chuck Norris', $blog2));
  		$this->assertNull($ts->getByLabelInBlog('sandwich 4 merguez', $blog));
  		$this->assertNull($ts->getByLabelInBlog('sandwich 4 merguez', $blog2));
  		$this->assertNull($ts->getByLabelInBlog('sauce au poivre !', $blog));
  		$this->assertNull($ts->getByLabelInBlog('sauce au poivre !', $blog2));
  		
  		$post3 = $btf->getNewPost($category12, array('category' => array($category12), 'keywordsText' => 'Chuck Norris, sandwich 4 merguez '));
  		
  		$this->assertNotNull($ts->getByLabelInBlog('Chuck Norris', $blog));
  		$this->assertNull($ts->getByLabelInBlog('Chuck Norris', $blog2));
  		$this->assertNotNull($ts->getByLabelInBlog('sandwich 4 merguez', $blog));
  		$this->assertNull($ts->getByLabelInBlog('sandwich 4 merguez', $blog2));
  		$this->assertNull($ts->getByLabelInBlog('sauce au poivre !', $blog));
  		$this->assertNull($ts->getByLabelInBlog('sauce au poivre !', $blog2));
  		
  		$post3->setKeywordsText('Chuck Norris,  sauce au poivre !');
  		$post3->save();
  		
  		$this->assertNotNull($ts->getByLabelInBlog('Chuck Norris', $blog));
  		$this->assertNull($ts->getByLabelInBlog('Chuck Norris', $blog2));
  		$this->assertNotNull($ts->getByLabelInBlog('sandwich 4 merguez', $blog));
  		$this->assertNull($ts->getByLabelInBlog('sandwich 4 merguez', $blog2));
  		$this->assertNotNull($ts->getByLabelInBlog('sauce au poivre !', $blog));
  		$this->assertNull($ts->getByLabelInBlog('sauce au poivre !', $blog2));
  	}
}