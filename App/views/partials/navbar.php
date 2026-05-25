<!-- Nav -->
<header class="bg-blue-900 text-white p-4">
  <div class="container mx-auto flex justify-between items-center">
    <h1 class="text-3xl font-semibold">
      <a href="/">Workopia</a>
    </h1>
    <nav class="space-x-4">
      <?php

      use Framework\Session;

      if (!Session::has('user')) : ?>
        <a href="/auth/login" class="text-white hover:underline">Login</a>
        <a href="/auth/create" class="text-white hover:underline">Register</a>
      <?php else : ?>
        <div class="inline">
          Welcome <?= Session::get('user')['name'] ?>
        </div>
        <!-- Logout Form -->
        <form method="POST" action="/auth/logout" class="inline">
          <input type="hidden" name="_method" value="delete" />
          <button type="submit" class="text-white hover:underline">Logout</button>
        </form>
        <a
          href="/listings/create"
          class="bg-yellow-500 hover:bg-yellow-600 text-black px-4 py-2 rounded hover:shadow-md transition duration-300"><i class="fa fa-edit"></i> Post a Job</a>
        <!-- End Logout Form -->
      <?php endif ?>
    </nav>
  </div>
</header>