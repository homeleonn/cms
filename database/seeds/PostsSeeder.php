<?php

use Illuminate\Database\Seeder;

class PostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Post::create([
        	'title'	=> 'Hello world!',
        	'slug'	=> 'hello-world',
        ]);

        DB::statement("insert into terms(name, slug, term_group) values ('Новости','new', 0), ('Статьи', 'articles', 0);");
        DB::statement("insert into term_taxonomy(term_id, taxonomy) values (1,'newscat'), (2,'newscat');");
        DB::statement("insert into term_relationships(object_id, term_taxonomy_id) values (1,1);");
    }
}
