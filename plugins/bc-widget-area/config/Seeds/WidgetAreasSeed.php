<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * WidgetAreas seed.
 */
class WidgetAreasSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'name' => '標準サイドバー',
                'widgets' => 'YTozOntpOjA7YToxOntzOjc6IldpZGdldDMiO2E6OTp7czoyOiJpZCI7czoxOiIzIjtzOjQ6InR5cGUiO3M6MzM6IuODreODvOOCq+ODq+ODiuODk+OCsuODvOOCt+ODp+ODsyI7czo3OiJlbGVtZW50IjtzOjEwOiJsb2NhbF9uYXZpIjtzOjY6InBsdWdpbiI7czowOiIiO3M6NDoic29ydCI7aToxO3M6NDoibmFtZSI7czozMzoi44Ot44O844Kr44Or44OK44OT44Ky44O844K344On44OzIjtzOjU6ImNhY2hlIjtzOjE6IjEiO3M6OToidXNlX3RpdGxlIjtzOjE6IjEiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319aToxO2E6MTp7czo3OiJXaWRnZXQyIjthOjg6e3M6MjoiaWQiO3M6MToiMiI7czo0OiJ0eXBlIjtzOjE4OiLjgrXjgqTjg4jlhoXmpJzntKIiO3M6NzoiZWxlbWVudCI7czo2OiJzZWFyY2giO3M6NjoicGx1Z2luIjtzOjA6IiI7czo0OiJzb3J0IjtpOjI7czo0OiJuYW1lIjtzOjE4OiLjgrXjgqTjg4jlhoXmpJzntKIiO3M6OToidXNlX3RpdGxlIjtzOjE6IjEiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319aToyO2E6MTp7czo3OiJXaWRnZXQxIjthOjk6e3M6MjoiaWQiO3M6MToiMSI7czo0OiJ0eXBlIjtzOjEyOiLjg4bjgq3jgrnjg4giO3M6NzoiZWxlbWVudCI7czo0OiJ0ZXh0IjtzOjY6InBsdWdpbiI7czowOiIiO3M6NDoic29ydCI7aTozO3M6NDoibmFtZSI7czo5OiLjg6rjg7Pjgq8iO3M6NDoidGV4dCI7czo0NDE6IjxwIHN0eWxlPSJtYXJnaW4tYm90dG9tOjIwcHg7dGV4dC1hbGlnbjogY2VudGVyIj4gPGEgaHJlZj0iaHR0cHM6Ly9iYXNlcmNtcy5uZXQiIHRhcmdldD0iX2JsYW5rIj48aW1nIHNyYz0iaHR0cDovL2Jhc2VyY21zLm5ldC9pbWcvYm5yX2Jhc2VyY21zLmpwZyIgYWx0PSLjgrPjg7zjg53jg6zjg7zjg4jjgrXjgqTjg4jjgavjgaHjgofjgYbjganjgYTjgYRDTVPjgIFiYXNlckNNUyIvPjwvYT48L3A+PHAgY2xhc3M9ImN1c3RvbWl6ZS1uYXZpIGNvcm5lcjEwIj48c21hbGw+44GT44Gu6YOo5YiG44Gv44CB566h55CG55S76Z2i44GuIFvoqK3lrppdIOKGkiBb44Om44O844OG44Kj44Oq44OG44KjXSDihpIgW+OCpuOCo+OCuOOCp+ODg+ODiOOCqOODquOCol0g4oaSIFvmqJnmupbjgrXjgqTjg4njg5Djg7xdIOOCiOOCiue3qOmbhuOBp+OBjeOBvuOBmeOAgjwvc21hbGw+PC9wPiI7czo5OiJ1c2VfdGl0bGUiO3M6MToiMSI7czo2OiJzdGF0dXMiO3M6MToiMSI7fX19',
                'modified' => '2020-12-14 14:34:10',
                'created' => '2015-06-26 20:34:07',
            ],
            [
                'id' => 2,
                'name' => 'ブログサイドバー',
                'widgets' => 'YTo2OntpOjA7YToxOntzOjc6IldpZGdldDciO2E6OTp7czoyOiJpZCI7czoxOiI3IjtzOjQ6InR5cGUiO3M6MjQ6IuODluODreOCsOOCq+ODrOODs+ODgOODvCI7czo3OiJlbGVtZW50IjtzOjEzOiJibG9nX2NhbGVuZGFyIjtzOjY6InBsdWdpbiI7czo5OiJCYXNlckNvcmUiO3M6NDoic29ydCI7aToxO3M6NDoibmFtZSI7czoyNDoi44OW44Ot44Kw44Kr44Os44Oz44OA44O8IjtzOjE1OiJibG9nX2NvbnRlbnRfaWQiO3M6MToiMSI7czo5OiJ1c2VfdGl0bGUiO3M6MToiMCI7czo2OiJzdGF0dXMiO3M6MToiMSI7fX1pOjE7YToxOntzOjc6IldpZGdldDgiO2E6MTM6e3M6MjoiaWQiO3M6MToiOCI7czo0OiJ0eXBlIjtzOjE4OiLjgqvjg4bjgrTjg6rkuIDopqciO3M6NzoiZWxlbWVudCI7czoyMjoiYmxvZ19jYXRlZ29yeV9hcmNoaXZlcyI7czo2OiJwbHVnaW4iO3M6OToiQmFzZXJDb3JlIjtzOjQ6InNvcnQiO2k6MjtzOjQ6Im5hbWUiO3M6MjE6IuOCq+ODhuOCtOODquODvOS4gOimpyI7czo1OiJsaW1pdCI7czowOiIiO3M6MTA6InZpZXdfY291bnQiO3M6MToiMSI7czo3OiJieV95ZWFyIjtzOjE6IjAiO3M6NToiZGVwdGgiO3M6MToiMSI7czoxNToiYmxvZ19jb250ZW50X2lkIjtzOjE6IjEiO3M6OToidXNlX3RpdGxlIjtzOjE6IjEiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319aToyO2E6MTp7czo3OiJXaWRnZXQ5IjthOjEwOntzOjI6ImlkIjtzOjE6IjkiO3M6NDoidHlwZSI7czoxNToi5pyA6L+R44Gu5oqV56i/IjtzOjc6ImVsZW1lbnQiO3M6MTk6ImJsb2dfcmVjZW50X2VudHJpZXMiO3M6NjoicGx1Z2luIjtzOjk6IkJhc2VyQ29yZSI7czo0OiJzb3J0IjtpOjM7czo0OiJuYW1lIjtzOjE1OiLmnIDov5Hjga7mipXnqL8iO3M6NToiY291bnQiO3M6MToiNSI7czoxNToiYmxvZ19jb250ZW50X2lkIjtzOjE6IjEiO3M6OToidXNlX3RpdGxlIjtzOjE6IjEiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319aTozO2E6MTp7czo4OiJXaWRnZXQxMCI7YToxMDp7czoyOiJpZCI7czoyOiIxMCI7czo0OiJ0eXBlIjtzOjI0OiLjg5bjg63jgrDmipXnqL/ogIXkuIDopqciO3M6NzoiZWxlbWVudCI7czoyMDoiYmxvZ19hdXRob3JfYXJjaGl2ZXMiO3M6NjoicGx1Z2luIjtzOjk6IkJhc2VyQ29yZSI7czo0OiJzb3J0IjtpOjQ7czo0OiJuYW1lIjtzOjI0OiLjg5bjg63jgrDmipXnqL/ogIXkuIDopqciO3M6MTA6InZpZXdfY291bnQiO3M6MToiMSI7czoxNToiYmxvZ19jb250ZW50X2lkIjtzOjE6IjEiO3M6OToidXNlX3RpdGxlIjtzOjE6IjEiO3M6Njoic3RhdHVzIjtzOjE6IjEiO319aTo0O2E6MTp7czo4OiJXaWRnZXQxMSI7YToxMTp7czoyOiJpZCI7czoyOiIxMSI7czo0OiJ0eXBlIjtzOjI3OiLmnIjliKXjgqLjg7zjgqvjgqTjg5bkuIDopqciO3M6NzoiZWxlbWVudCI7czoyMToiYmxvZ19tb250aGx5X2FyY2hpdmVzIjtzOjY6InBsdWdpbiI7czo5OiJCYXNlckNvcmUiO3M6NDoic29ydCI7aTo1O3M6NDoibmFtZSI7czoyNzoi5pyI5Yil44Ki44O844Kr44Kk44OW5LiA6KanIjtzOjU6ImxpbWl0IjtzOjI6IjEyIjtzOjEwOiJ2aWV3X2NvdW50IjtzOjE6IjEiO3M6MTU6ImJsb2dfY29udGVudF9pZCI7czoxOiIxIjtzOjk6InVzZV90aXRsZSI7czoxOiIxIjtzOjY6InN0YXR1cyI7czoxOiIxIjt9fWk6NTthOjE6e3M6ODoiV2lkZ2V0MTIiO2E6MTI6e3M6MjoiaWQiO3M6MjoiMTIiO3M6NDoidHlwZSI7czoyNzoi5bm05Yil44Ki44O844Kr44Kk44OW5LiA6KanIjtzOjc6ImVsZW1lbnQiO3M6MjA6ImJsb2dfeWVhcmx5X2FyY2hpdmVzIjtzOjY6InBsdWdpbiI7czo5OiJCYXNlckNvcmUiO3M6NDoic29ydCI7aTo2O3M6NDoibmFtZSI7czoyNzoi5bm05Yil44Ki44O844Kr44Kk44OW5LiA6KanIjtzOjU6ImxpbWl0IjtzOjA6IiI7czoxMDoidmlld19jb3VudCI7czoxOiIxIjtzOjExOiJzdGFydF9tb250aCI7czoxOiIxIjtzOjE1OiJibG9nX2NvbnRlbnRfaWQiO3M6MToiMSI7czo5OiJ1c2VfdGl0bGUiO3M6MToiMSI7czo2OiJzdGF0dXMiO3M6MToiMSI7fX19',
                'modified' => '2020-09-14 20:16:49',
                'created' => '2015-06-26 20:34:07',
            ],
        ];

        $table = $this->table('widget_areas');
        $table->insert($data)->save();
    }
}
