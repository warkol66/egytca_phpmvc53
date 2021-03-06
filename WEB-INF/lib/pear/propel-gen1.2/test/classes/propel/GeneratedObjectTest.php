<?php
/*
 *  $Id: GeneratedObjectTest.php 566 2007-02-02 16:27:12Z hans $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://propel.phpdb.org>.
 */

require_once 'bookstore/BookstoreTestBase.php';

/**
 * Tests the generated Object classes.
 *
 * This test uses generated Bookstore classes to test the behavior of various
 * object operations.  The _idea_ here is to test every possible generated method
 * from Object.tpl; if necessary, bookstore will be expanded to accommodate this.
 *
 * The database is relaoded before every test and flushed after every test.  This
 * means that you can always rely on the contents of the databases being the same
 * for each test method in this class.  See the BookstoreDataPopulator::populate()
 * method for the exact contents of the database.
 *
 * @see        BookstoreDataPopulator
 * @author     Hans Lellelid <hans@xmpl.org>
 */
class GeneratedObjectTest extends BookstoreTestBase {

	/**
	 * Test saving an object after setting default values for it.
	 */
	public function testSaveWithDefaultValues() {

		// From the schema.xml, I am relying on the following:
		//  - that 'Penguin' is the default Name for a Publisher
		//  - that 01/01/2001 is the default ReviewDate for a Review

		// 1) check regular values (VARCHAR)
		$pub = new Publisher();
		$pub->setName('Penguin');
		$pub->save();
		$this->assertTrue($pub->getId() !== null, "Expect Publisher to have been saved when default value set.");

		// 2) check date/time values
		$review = new Review();
		// note that this is different from how it's represented in schema, but should resolve to same unix timestamp
		$review->setReviewDate('2001-01-01');
		$this->assertTrue($review->isModified(), "Expect Review to have been marked 'modified' after default date/time value set.");

	}

	/**
	 * Test saving an object and getting correct number of affected rows from save().
	 * This includes tests of cascading saves to fk-related objects.
	 */
	public function testSaveReturnValues()
	{

		$author = new Author();
		$author->setFirstName("Mark");
		$author->setLastName("Kurlansky");
		// do not save

		$pub = new Publisher();
		$pub->setName("Penguin Books");
		// do not save

		$book = new Book();
		$book->setTitle("Salt: A World History");
		$book->setISBN("0142001619");
		$book->setAuthor($author);
		$book->setPublisher($pub);

		$affected = $book->save();
		$this->assertEquals(3, $affected, "Expected 3 affected rows when saving book + publisher + author.");

		// change nothing ...
		$affected = $book->save();
		$this->assertEquals(0, $affected, "Expected 0 affected rows when saving already-saved book.");

		// modify the book (UPDATE)
		$book->setTitle("Salt A World History");
		$affected = $book->save();
		$this->assertEquals(1, $affected, "Expected 1 affected row when saving modified book.");

		// modify the related author
		$author->setLastName("Kurlanski");
  		$affected = $book->save();
		$this->assertEquals(1, $affected, "Expected 1 affected row when saving book with updated author.");

		// modify both the related author and the book
		$author->setLastName("Kurlansky");
		$book->setTitle("Salt: A World History");
  		$affected = $book->save();
		$this->assertEquals(2, $affected, "Expected 2 affected rows when saving updated book with updated author.");

	}

	/**
	 * Test deleting an object using the delete() method.
	 */
	public function testDelete() {

		// 1) grab an arbitrary object
		$book = BookPeer::doSelectOne(new Criteria());
		$bookId = $book->getId();

		// 2) delete it
		$book->delete();

		// 3) make sure it can't be save()d now that it's deleted
		try {
			$book->setTitle("Will Fail");
			$book->save();
			$this->fail("Expect an exception to be thrown when attempting to save() a deleted object.");
		} catch (PropelException $e) {}

		// 4) make sure that it doesn't exist in db
		$book = BookPeer::retrieveByPK($bookId);
		$this->assertNull($book, "Expect NULL from retrieveByPK on deleted Book.");

	}

	/**
	 *
	 */
	public function testNoColsModified()
	{
		$e1 = new BookstoreEmployee();
		$e1->setName('Employee 1');

		$e2 = new BookstoreEmployee();
		$e2->setName('Employee 2');

		$super = new BookstoreEmployee();
		// we don't know who the supervisor is yet
		$super->addBookstoreEmployeeRelatedBySupervisorId($e1);
		$super->addBookstoreEmployeeRelatedBySupervisorId($e2);

		$affected = $super->save();

	}
	
	/**
	 * Test for correct reporting of isModified().
	 */
	public function testIsModified()
	{
		// 1) Basic test
		
		$a = new Author();
		$a->setFirstName("John");
		$a->setLastName("Doe");
		$a->setAge(25);
		
		$this->assertTrue($a->isModified(), "Expected Author to be modified after setting values.");
		
		$a->save();
		
		$this->assertFalse($a->isModified(), "Expected Author to be unmodified after saving set values.");
		
		// 2) Test behavior with setting vars of different types
		
		// checking setting int col to string val
		$a->setAge('25');
		$this->assertFalse($a->isModified(), "Expected Author to be unmodified after setting int column to string-cast of same value.");
		
		$a->setFirstName("John2");
		$this->assertTrue($a->isModified(), "Expected Author to be modified after changing string column value.");
		
		// checking setting string col to int val
		$a->setFirstName("1");
		$a->save();
		$this->assertFalse($a->isModified(), "Expected Author to be unmodified after saving set values.");
		
		$a->setFirstName(1);
		$this->assertFalse($a->isModified(), "Expected Author to be unmodified after setting string column to int-cast of same value.");
		
		// 3) Test for appropriate behavior of NULL
		
		// checking "" -> NULL 
		$a->setFirstName("");
		$a->save();
		$this->assertFalse($a->isModified(), "Expected Author to be unmodified after saving set values.");
		
		$a->setFirstName(null);
		$this->assertTrue($a->isModified(), "Expected Author to be modified after changing empty string column value to NULL.");
		
		$a->setFirstName("John");
		$a->setAge(0);
		$a->save();
		$this->assertFalse($a->isModified(), "Expected Author to be unmodified after saving set values.");
		
		$a->setAge(null);
		$this->assertTrue($a->isModified(), "Expected Author to be modified after changing 0-value int column to NULL.");
		
		$a->save();
		$this->assertFalse($a->isModified(), "Expected Author to be unmodified after saving set values.");
		
		$a->setAge(0);
		$this->assertTrue($a->isModified(), "Expected Author to be modified after changing NULL-value int column to 0.");
		
	}
}
