<label class="flex flex-col text-black" id="filter-num">
   <div class="relative mt-4 m-4">
       <div class="flex flex-row justify-between mb-4">
           <div class="flex items-center">
               <input type="number" step="0.01" id="min-value-detailed" class="w-16 px-2 py-1 border rounded text-black" value="0.10" min="0" max="100">
           </div>
           <div class="flex items-center">
               <input type="number" step="0.01" id="max-value-detailed" class="w-16 px-2 py-1 border rounded text-black" value="100" min="0" max="100">
           </div>
       </div>

       <div class="relative h-2 bg-gray-200 rounded">
           <div class="absolute h-full bg-emerald-300 rounded" id="detailed-range-bar"></div>
           <input type="range" id="min-range-detailed" min="0" max="100" value="0.10" step="0.01" class="absolute w-full h-full opacity-0 cursor-pointer">
           <input type="range" id="max-range-detailed" min="0" max="100" value="100" step="0.01" class="absolute w-full h-full opacity-0 cursor-pointer">
       </div>
   </div>
</label>

<script>
$(document).ready(function() {
   const minRange = $('#min-range-detailed');
   const maxRange = $('#max-range-detailed');
   const minValue = $('#min-value-detailed');
   const maxValue = $('#max-value-detailed');
   const rangeBar = $('#detailed-range-bar');

   function updateRangeBar() {
       const min = parseInt(minRange.val());
       const max = parseInt(maxRange.val());
       rangeBar.css({
           'left': min + '%',
           'right': (100 - max) + '%'
       });
   }

   minRange.on('input', function() {
       const min = parseInt($(this).val());
       const max = parseInt(maxRange.val());
       
       if (min <= max) {
           minValue.val(min);
           updateRangeBar();
       } else {
           $(this).val(max);
       }
   });

   maxRange.on('input', function() {
       const max = parseInt($(this).val());
       const min = parseInt(minRange.val());
       
       if (max >= min) {
           maxValue.val(max);
           updateRangeBar();
       } else {
           $(this).val(min);
       }
   });

   minValue.on('input', function() {
       const min = parseInt($(this).val()) || 0;
       const max = parseInt(maxValue.val());
       
       if (min <= max && min >= 0 && min <= 100) {
           minRange.val(min);
           updateRangeBar();
       }
   });

   maxValue.on('input', function() {
       const max = parseInt($(this).val()) || 0;
       const min = parseInt(minValue.val());
       
       if (max >= min && max >= 0 && max <= 100) {
           maxRange.val(max);
           updateRangeBar();
       }
   });

   // Initialize the range bar
   updateRangeBar();
});
</script>
