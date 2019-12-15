<?php


namespace Tests\Feature;

use App\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookReservationTest extends TestCase
{
    use refreshDatabase;

    /** @test */
    public function a_book_can_be_added_to_the_library()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('/books', [
            'title' => 'cool book title',
            'author' => 'harry'
        ]);

        $response->assertOk();
        $this->assertCount(1, Book::all());
    }

    /** @test */
    public function a_title_is_required()
    {
        // $this->withoutExceptionHandling();
        $response = $this->post('/books', [
            'title' => '',
            'author' => 'harry'
        ]);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function an_author_is_required()
    {
        $response = $this->post('/books', [
            'title' => 'cool book title',
            'author' => ''
        ]);

        $response->assertSessionHasErrors('author');
    }

    /** @test */
    public function a_book_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $this->post('/books', [
            'title' => 'cool book title',
            'author' => 'harry'
        ]);

        $book = Book::first();

        $response = $this->patch('/books/' . $book->id, [
            'title' => 'New Title',
            'author' => 'victor',
        ]);

        $this->assertEquals('New Title', Book::first()->title);
        $this->assertEquals('victor', Book::first()->author);
    }
}

