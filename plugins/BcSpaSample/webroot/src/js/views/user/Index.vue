<template>
    <div>
        <section id="DataList">
            <table class="list-table bca-table-listup" id="ListTable">
                <thead class="bca-table-listup__thead">
                <tr>
                    <th class="bca-table-listup__thead-th">
                       No</th>
                    <th class="bca-table-listup__thead-th">
                        アカウント名</th>
                    <th class="bca-table-listup__thead-th">
                        Eメール</th>
                    <th class="bca-table-listup__thead-th">
                        ニックネーム</th>
                    <th class="bca-table-listup__thead-th">
                        グループ
                    </th>
                    <th class="bca-table-listup__thead-th">
                        氏名</th>
                    <th class="bca-table-listup__thead-th">
                        登録日<br>
                        更新日</th>
                    <th class="bca-table-listup__thead-th">アクション</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(user, i) in users" :key="i">
                    <td class="bca-table-listup__tbody-td">{{user.id}}</td>
                    <td class="bca-table-listup__tbody-td">
                        <router-link :to="{ path: 'user_edit' + '/' + user.id }">{{user.name}}</router-link>
                    </td>
                    <td class="bca-table-listup__tbody-td">{{user.email}}</td>
                    <td class="bca-table-listup__tbody-td">{{user.nickname}}</td>
                    <td class="bca-table-listup__tbody-td">
                        <ul class="user_group" v-for="(userGroup, i) in user.user_groups" :key="i">
                            <li>{{userGroup.title}}</li>
                        </ul>
                    </td>
                    <td class="bca-table-listup__tbody-td">{{user.real_name_1}}&nbsp;{{user.real_name_2}}</td>
                    <td class="bca-table-listup__tbody-td">{{user.created}}<br>
                        {{user.modified}}
                    </td>
                    <td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
                        <router-link title="編集" class=" bca-btn-icon" data-bca-btn-type="edit" data-bca-btn-size="lg" :to="{ path: 'user_edit' + '/' + user.id }"></router-link>
                    </td>
                </tr>
                </tbody>
            </table>
        </section>
    </div>
</template>
<script lang="ts" src="./Index.ts"></script>

