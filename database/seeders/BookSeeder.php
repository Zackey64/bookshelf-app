<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 登録者
        $user = User::first();

        // 事前に登録されているジャンルを取得
        $genres = Genre::all();

        // 指定された11件を定義
        $booksData = [
            [
                'user_id' => $user->id,
                'title' => '吾輩は猫である',
                'author' => '夏目漱石',
                'isbn' => '9784101010014',
                'published_date' => '1905-01-01',
                'description' => '「吾輩は猫である。名前はまだ無い。」で始まる、猫の視点から人間模様を風刺的に描いた夏目漱石の不朽の名作。',
                'genres' => ['小説'],
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=1',
            ],
            [
                'user_id' => $user->id,
                'title' => '人を動かす',
                'author' => 'D・カーネギー',
                'isbn' => '9784422100524',
                'published_date' => '1936-10-01',
                'description' => 'あらゆる人間関係の原則を説いた歴史的ベストセラー。ビジネスから私生活まで役立つコミュニケーションの極意。',
                'genres' => ['ビジネス', '自己啓発'],
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=2',
            ],
            [
                'user_id' => $user->id,
                'title' => 'リーダブルコード',
                'author' => 'Dustin Boswell',
                'isbn' => '9784873115658',
                'published_date' => '2012-06-23',
                'description' => '「美しいコードとは何か」を追求し、読みやすく直感的なコードを書くための実践的な手法をまとめたエンジニア必読の書。',
                'genres' => ['技術書'],
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=3',
            ],
            [
                'user_id' => $user->id,
                'title' => '7つの習慣',
                'author' => 'スティーブン・R・コヴィー',
                'isbn' => '9784863940246',
                'published_date' => '2013-08-30',
                'description' => '全世界でベストセラーとなった成功哲学の最高峰。人格を磨き、効果的な人生を送るためのタイムレスな原則。',
                'genres' => ['ビジネス', '自己啓発'],
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=4',
            ],
            [
                'user_id' => $user->id,
                'title' => '坊っちゃん',
                'author' => '夏目漱石',
                'isbn' => '9784101010021',
                'published_date' => '1906-04-01',
                'description' => '江戸っ子気質で正義感の強い「坊っちゃん」が、四国の中学校で繰り広げる人間模様を描いた爽快なユーモア小説。',
                'genres' => ['小説'],
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=5',
            ],
            [
                'user_id' => $user->id,
                'title' => 'サピエンス全史',
                'author' => 'ユヴァル・ノア・ハラリ',
                'isbn' => '9784309226712',
                'published_date' => '2016-09-08',
                'description' => 'ホモ・サピエンスがなぜ地球の支配者になれたのか。認知革命、農業革命、科学革命を軸に人類の歴史を解き明かす。',
                'genres' => ['歴史', '科学'],
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=6',
            ],
            [
                'user_id' => $user->id,
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'isbn' => '9784048930598',
                'published_date' => '2017-12-18',
                'description' => 'プロフェッショナルな職人として、保守性が高く洗練されたアジャイルソフトウェアのコードを書くための指針。',
                'genres' => ['技術書'],
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=7',
            ],
            [
                'user_id' => $user->id,
                'title' => '嫌われる勇気',
                'author' => '岸見一郎・古賀史健',
                'isbn' => '9784478025819',
                'published_date' => '2013-12-13',
                'description' => 'アルフレッド・アドラーの「アドラー心理学」を、哲学者と青年の対話形式で分かりやすく解説したミリオンセラー。',
                'genres' => ['自己啓発'],
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=8',
            ],
            [
                'user_id' => $user->id,
                'title' => '火花',
                'author' => '又吉直樹',
                'isbn' => '9784163902302',
                'published_date' => '2015-03-11',
                'description' => '売れない芸人・徳永と、天才肌の先輩芸人・神谷の交流を通じ、笑いとは何か、生きるとは何かを描いた芥川賞受賞作。',
                'genres' => ['小説'],
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=9',
            ],
            [
                'user_id' => $user->id,
                'title' => 'FACTFULNESS',
                'author' => 'ハンス・ロスリング',
                'isbn' => '9784822289607',
                'published_date' => '2019-01-11',
                'description' => 'データや事実に基づき、世界を正しく見る習慣（ファクトフルネス）を提唱。思い込みを排して世界を読み解く一冊。',
                'genres' => ['ビジネス', '科学'],
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=10',
            ],
            [
                'user_id' => $user->id,
                'title' => 'コンテナ物語',
                'author' => 'マルク・レビンソン',
                'isbn' => '9784822251468',
                'published_date' => '2007-01-18',
                'description' => 'たった一枚の「鉄の箱（コンテナ）」が世界の物流、地政学、そして経済の仕組みを劇的に変えた壮大なドキュメンタリー。',
                'genres' => ['ビジネス', '歴史'],
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=11',
            ],
        ];

        // ループ処理でテーブルに投入
        foreach ($booksData as $data) {
            // 書籍を登録
            $book = Book::firstOrCreate(
                ['isbn' => $data['isbn']],
                [
                    'user_id' => $data['user_id'],
                    'title' => $data['title'],
                    'author' => $data['author'],
                    'published_date' => $data['published_date'],
                    'description' => $data['description'],
                    'image_url' => $data['image_url'],
                ]
            );
            // ジャンル
            $genreIds = $genres->whereIn('name', $data['genres'])->pluck('id')->toArray();
            $book->genres()->sync($genreIds);
        }

    }
}
