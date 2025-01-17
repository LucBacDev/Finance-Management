<template>
       <aside id="sidebar" class="js-sidebar">
            <!-- Content For Sidebar -->
            <div class="h-100">
                <div class="sidebar-logo">
                    <a href="#">CodzSword</a>
                </div>
                <ul class="sidebar-nav">
                    
                    <li class="sidebar-item">
                        <NuxtLink to="/home" class="sidebar-link">
                            <i class="fa-solid fa-house pe-2"></i> 
                           Home
                        </NuxtLink>
                    </li>
                    <li class="sidebar-item">
                        <NuxtLink to="/category/list" class="sidebar-link collapsed" 
                            aria-expanded="false"><i class="fa-solid fa-list pe-2"></i>
                            Category
                        </NuxtLink>
                    </li>
           
                    <li class="sidebar-item">
                        <NuxtLink to="/income/list" class="sidebar-link collapsed" 
                            aria-expanded="false"><i class="fa-solid fa-file-lines pe-2"></i>
                            Income
                        </NuxtLink>
                    </li>
                    <li class="sidebar-item">
                        <NuxtLink to="/expense/list" class="sidebar-link collapsed" 
                            aria-expanded="false"><i class="fa-solid fa-money-bill pe-2 "></i>
                            Expense
                        </NuxtLink>
                    </li>
                    <li class="sidebar-item">
                        <NuxtLink to="/reminder/list" class="sidebar-link collapsed" 
                            aria-expanded="false"><i class="fa-solid fa-bell pe-2"></i>
                            Reminder
                        </NuxtLink>
                    </li>
                    <li class="sidebar-item">
                        <NuxtLink to="/statistic/day" class="sidebar-link collapsed" 
                            aria-expanded="false"><i class="fa-solid fa-chart-simple pe-2"></i>
                            Statistic
                        </NuxtLink>
                    </li>
                </ul>
            </div>
        </aside>
</template>

<script>
export default {
    setup () {
        

        return {}
    }
}
</script>

<style lang="scss" scoped>

</style>