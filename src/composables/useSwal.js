import Swal from 'sweetalert2'

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.onmouseenter = Swal.stopTimer
    toast.onmouseleave = Swal.resumeTimer
  },
})

export const useSwal = () => {
  const success = (title = 'Berhasil!', message = '') => {
    return Swal.fire({
      icon: 'success',
      title,
      text: message,
      confirmButtonColor: '#033d4a',
    })
  }

  const error = (title = 'Gagal!', message = '') => {
    return Swal.fire({
      icon: 'error',
      title,
      text: message,
      confirmButtonColor: '#dc3545',
    })
  }

  const warning = (title = 'Peringatan!', message = '') => {
    return Swal.fire({
      icon: 'warning',
      title,
      text: message,
      confirmButtonColor: '#ffc107',
    })
  }

  const confirm = (title = 'Konfirmasi', message = 'Apakah Anda yakin?') => {
    return Swal.fire({
      icon: 'question',
      title,
      text: message,
      showCancelButton: true,
      confirmButtonColor: '#033d4a',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, lanjutkan!',
      cancelButtonText: 'Batal',
    })
  }

  const confirmDelete = (itemName = 'item ini') => {
    return Swal.fire({
      icon: 'warning',
      title: 'Hapus ' + itemName + '?',
      text: 'Tindakan ini tidak dapat dibatalkan!',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal',
    })
  }

  const confirmDanger = (title = 'Peringatan', message = 'Tindakan ini berbahaya. Yakin lanjutkan?', confirmBtn = 'Ya, lanjutkan!') => {
    return Swal.fire({
      icon: 'warning',
      title: title,
      text: message,
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: confirmBtn,
      cancelButtonText: 'Batal',
    })
  }

  const toast = (icon = 'success', title = '') => {
    return Toast.fire({
      icon,
      title,
    })
  }

  const loading = (title = 'Memproses...') => {
    return Swal.fire({
      title,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading()
      },
    })
  }

  const close = () => {
    Swal.close()
  }

  const closeLoading = () => {
    Swal.hideLoading()
  }

  return {
    success,
    error,
    warning,
    confirm,
    confirmDelete,
    confirmDanger,
    toast,
    loading,
    close,
    closeLoading,
    Swal,
  }
}
