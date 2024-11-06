$('.wrapper').find('a[href="#"]').on('click', function (e) {
    e.preventDefault();
    this.expand = !this.expand;
    $(this).text(this.expand?"Hide":"Read More");
    $(this).closest('.wrapper').find('.read-less, .read-more').toggleClass('read-less read-more');
});